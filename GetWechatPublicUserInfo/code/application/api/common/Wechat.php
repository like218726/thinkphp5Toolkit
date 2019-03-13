<?php
/**
 * Created by PhpStorm.
 * User: hutuo
 * Date: 2017/7/13
 * Time: 11:06
 */
namespace app\api\common;

use app\api\common\HttpCurl;

class Wechat{
    const WX_APP_ID      = "wx7168170b6690427e";
    const WX_APP_SECRET = "bc9afb98a2b1779b337c0ab7ba01d339";
    const WX_MCH_ID      = "1494494532";
    const WX_KEY         = "9902024bcc176cedd435d78939684f99";

    /**
     * 微信网页授权
     * @param string $redirect_url  授权后重定向的回调链接地址，请使用urlEncode对链接进行处理
     * @param string $scope          静默授权或者非静默授权 snsapi_base 或者 snsapi_userinfo
     * @param string $state         重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
     */
    public function oAuth ( $redirect_url ,$scope = "snsapi_userinfo" , $state = "123" )
    {
        $redirect_url = urlencode($redirect_url);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::WX_APP_ID."&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header('Location: '.$url);
        exit;
    }

    /**
     * 通过微信code获取用户授权数据包
     * @param $code
     * @return mixed|string
     */
    public function getAccessTokenByCode ( $code )
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::WX_APP_ID."&secret=".self::WX_APP_SECRET."&code={$code}&grant_type=authorization_code ";
        $data = HttpCurl::curlRequest($url);
        return $data;
    }

    /**
     * 通过OPENID获取用户信息
     * @param $openid
     * @return mixed|string
     */
    public function getUserInfo ($token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
        $data = HttpCurl::curlRequest($url);
        return $data;
    }

    /**
     * 获取app信息数组
     * @return array
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol"."$_SERVER[HTTP_HOST]/";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $param = [];
        $param['jsapi_ticket'] = $jsapiTicket;
        $param['noncestr']     = $nonceStr;
        $param['timestamp']    = $timestamp;
        $param['url']           = $url;

        $string = $this -> formatBizQueryParaMap($param);

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => self::WX_APP_ID,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**
     * 获取token值
     * @return mixed|string
     */
    public function getAccessToken ()
    {
        if(!file_exists("./static/wechat/json/access_token.json")) {
            fopen("./static/wechat/json/access_token.json", "w");
        }

        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("./static/wechat/json/access_token.json"));

        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".self::WX_APP_ID."&secret=".self::WX_APP_SECRET;
            $res = json_decode(HttpCurl::curlRequest($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("./static/wechat/json/access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    /**
     * 获取jsapi_ticket值
     * @return mixed|string
     */
    public function getJsApiTicket ()
    {
        if(!file_exists("./static/wechat/json/jsapi_ticket.json")) {
            fopen("./static/wechat/json/jsapi_ticket.json", "w");
        }

        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("./static/wechat/json/jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(HttpCurl::curlRequest($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("./static/wechat/json/jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    /**
     *
     * 发送模板消息
     *
     **/
    public function sendTemplateMsg($data){
        $data = json_encode($data);

        $access_token = $this->getAccessToken();
        $url  = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
        return HttpCurl::httpRequest($url, "POST", $data);
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
}