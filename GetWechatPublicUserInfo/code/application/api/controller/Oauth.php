<?php
namespace app\api\controller;

use think\Db;

use app\api\model\Member;

use app\api\common\Wechat;
use think\Cache;
use think\Controller;
use think\Cookie;

class Oauth extends Controller
{
    //定义是否检测登录
    public static $_NeedCheckLogin = false;

    /**
     * @desc 用户授权回调
     */
    public function index()
    {
        $code  = input("code/s");
        $reurl = input("reurl/s");

        //获取微信openid
        $wechat = new Wechat();
        $wx_info = $wechat->getAccessTokenByCode($code);
        $wx_info = json_decode($wx_info,true);
        $openid  = $wx_info['openid'];
        $access_token = $wx_info['access_token'];

        $userInfo = $wechat->getUserInfo($access_token, $openid);
        $userInfo = json_decode($userInfo,true);

        $nickname = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $userInfo['nickname']);
        $sex      = $userInfo['sex'];
        $province = $userInfo['province'];
        $city     = $userInfo['city'];
        $headimg  = $userInfo['headimgurl'];

        $memberinfo = model('Member')->getMemberInfoByOpenid($openid);

        if(empty($memberinfo))
        {
            $option = [];
            $option['openId']   = $openid;
            $option['nickname'] = $nickname;
            $option['sex']       = $sex;
            $option['last_province'] = $province;
            $option['last_city']      = $city;
            $option['head_pic']  = $headimg;
            $option['regTime']  = time();
            $option['last_login'] = time();
            $option['wx_nickname'] = $nickname;
            $option['wx_head_pic'] = $headimg;
            $option['wx_time'] = date('Y-m-d H:i:s',time());

            Db::name('member')->insert($option);
            $member_id = Db::name('member')->getLastInsID();
            Cookie::set('member_id', $member_id);
        } else {
            Cookie::set('member_id', $memberinfo['id']);
        }
        $this->redirect($reurl);
    }
    
    public function delCookie()
    {
    	Cookie::clear('member_id');
    	exit('清除成功');
    }
}
