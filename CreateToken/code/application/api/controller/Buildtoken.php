<?php
/**
 * Created by PhpStorm.
 * User: hutuo
 * Date: 2018/11/02
 * Time: 09:21
 */

namespace app\api\controller;

use think\Cookie;

use think\Cache;
use think\Request;

class Buildtoken
{
    protected $_APP_ID = "qnAd3owAPaQE15438178";
    protected $_APP_SECERT = "3a8418d1856e8752a7432c80b363112e";
    protected $expires = 86400;

    public function getAccessToken($param)
    {
    	
        if (empty($param['app_id'])) {
            return $this->ajaxError('缺少app_id', -801);
        }

        if ($param['app_id'] != $this->_APP_ID) {
            return $this->ajaxError('app_id非法', -802);
        }

        if (empty($param['signature'])){
            return $this->ajaxError('缺少签名', -803);
        }

        if (empty($param['member_id'])){
            return $this->ajaxError('缺少用户id', -804);
        }

        $signature = $param['signature'];
        unset($param['signature']);
        $sign = $this->getAuthToken($this->_APP_SECERT, $param);
        if ($sign !== $signature) {
            return $this->ajaxError('身份令牌验证失败', -805);
        }
        
        $expires = $this->expires;
        $accessToken = Cache::get("TOKEN_".$param['member_id']);
        if ($accessToken) {
            Cache::clear("TOKEN_".$param['member_id']);
        }

        $accessToken = md5($this->_APP_ID.$this->_APP_SECERT.$param['member_id'].rand(1000000,999999)); 
        Cache::set("TOKEN_".$param['member_id'], $accessToken, $expires);  
		Cache::set("member_id",$param['member_id'],$expires);
//		Cookie::set('member_id',$param['member_id'],$expires);    
        $return['access_token'] = $accessToken;  
        $return['expires_in'] = $expires;
//		$return['member_id'] = $param['member_id'];
//		$return['TOKEN_'.$param['member_id']] = $accessToken;
        return json($return);
    }

    public function ajaxSuccess( $msg, $code = 1, $data = array() ){
        $returnData = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        if( !empty($this->debug) ){
            $returnData['debug'] = $this->debug;
        }
        return json($returnData);
    }

    public function ajaxError( $msg, $code = 0, $data = array() ){
        $returnData = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        if( !empty($this->debug) ){
            $returnData['debug'] = $this->debug;
        }

        return json($returnData);
    }

    /**
     * 根据AppSecret和数据生成相对应的身份认证秘钥
     * @param $appSecret
     * @param $data
     * @return string
     */
    public function getAuthToken( $appSecret, $data ){
        if(empty($data)){
            return '';
        }else{
            $preArr = array_merge($data, array('app_secret' => $appSecret));
            ksort($preArr);
            $preStr = http_build_query($preArr);
            return md5($preStr);
        }
    }
}
