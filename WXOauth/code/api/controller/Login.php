<?php
/**
 * Created by PhpStorm.
 * User: lijunhua
 * Date: 2019/1/23
 * Time: 15:57
 */

namespace app\api\controller;

use think\Cookie;

use app\api\model\VerificationCode;

use app\common\HttpCurl;
use think\Controller;

class Login extends Controller
{
    const WX_APP_ID      = "wx2749509348d8bfc3";
    const WX_APP_SECRET = "7214d7a1cea4a7bb82eee5b509a1b348";

    public function getUser(){
        $code = input('post.code/s');

        if (empty($code)){
            return json(['code'=>0,'msg'=>'参数错误']);
        }

        $res = $this -> getOpendId($code);
        if(!empty($res['errcode'])){
            return json(['code'=>0,'msg'=>$res['errmsg']]);
        }

        $openid = $res['openid'];
        Cookie::set('openid', $openid);
        $user = model('User')->get(['openid'=>$openid]);
        if(empty($user)){
            return ajaxSuccess('',1,['uid'=>0,'openid'=>$openid]);
        }

        return ajaxSuccess('',1,['uid'=>$user['id'],'openid'=>$openid]);
    }

    public function register(){
        $openid = input("post.openid/s");
        $mobile = input('post.mobile/s');
		$ver_code = input('post.ver_code/d');
        if(empty($openid))
        {
            return ajaxError('参数错误');
        }

        if(!preg_match("/^1[3-9]{1}\d{9}$/",$mobile))
        {
            return ajaxError('请正确输入手机号码');
        }

        $user = model('User')->get(['mobile'=>$mobile]);
        if(!empty($user))
        {
            return ajaxError('手机号码已注册');
        }
        
    	if(!preg_match("/^\d{6}$/",$ver_code)){
			return ajaxError("验证码必须为6位的数字");
	    }               

		$where = array();
		$where['telphone'] = $mobile;
		$where['verification_code'] = $ver_code;
		$verification_code = new VerificationCode(); 
		$ver_code_info = $verification_code->getVerificationCodeByCondition($where);
		if (!$ver_code_info) {
			return ajaxError("验证码非法");
		}
		if ($ver_code_info['is_used'] == 1) {
			return ajaxError("此验证码已被使用");
		}
		if (time() - $ver_code_info['create_time'] > 600) {
			return ajaxError("此验证码已超过有效期10分钟");
		}	    
	    
        $user = model('User')->get(['openid'=>$openid]);
        if(!empty($user))
        {
            return ajaxError('微信用户已注册');
        }

        $data = [];
        $data['nickname'] = input("post.nickName") ? input("post.nickName") : '';
        $data['sex'] = input("post.gender/d") ? input("post.gender/d") : 0;
        $data['headimg'] = input("post.avatarUrl/s") ? input("post.avatarUrl/s") : '';
        $data['country'] = input("post.country/s") ? input("post.country/s") : '';
        $data['province'] = input("post.province/s") ? input("post.province/s") : '';
        $data['city'] = input("post.city/s") ? input("post.city/s") : '';

        model('VerificationCode')->where($where)->update(['is_used'=>1]);
        
        $user = model('WxUser')->get(['openid'=>$openid]);
        if(empty($user))
        {
            $data['openid'] = $openid;
            $data['addtime'] = time();
            model('WxUser')->save($data);
        }

        $info = [];
        
        $info['openid'] = $openid;
        $info['mobile'] = $mobile;
        $info['bing_phone'] = $mobile;
        $info['user_nick'] = $data['nickname'];
        $info['wx_nickname'] = $data['nickname'];
        $info['wx_head_pic'] = $data['headimg'];
        $info['bing_time'] = date('Y-m-d H:i:s');
        $info['sex'] = input("post.gender/d") ? input("post.gender/d") : 0;
        $info['create_time'] = date('Y-m-d H:i:s');
        model('User')->save($info);
        $uid = model('User')->getLastInsID();

        return ajaxSuccess('保存成功', 1, ['uid'=>$uid]);
    }

    private function getOpendId($code){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".self::WX_APP_ID."&secret=".self::WX_APP_SECRET."&js_code=".$code."&grant_type=authorization_code";
        $res = json_decode(HttpCurl::curlRequest($url), true);

        if(empty($res['errcode']))
        {
            $openid = $res['openid'];
            return ['openid'=>$openid];
        }else{
            return ['errcode'=>$res['errcode'], 'errmsg'=>$res['errmsg']];
        }
    }
}