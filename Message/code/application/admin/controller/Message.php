<?php
namespace app\api\controller;

use think\Controller;

use think\Db;

class Message extends Controller {
	
	/**
	 * 
	 * 发送验证码
	 */
	public function SendSms() {
		if ($this->request->isPost()) {
			$code = rand(100000, 999999);
			$telphone = input('post.telphone','','trim');
			
			//判断是否存在验证码
			$data = model("VerificationCode")->where(array('telphone'=>$telphone, 'is_used'=>0))->order('id DESC')->find();
			//获取时间配置
			$alicloud = config('ALICLOUD');	
			$sms_time_out = $alicloud['sms_time_out'];
			$sms_time_out = $sms_time_out ? $sms_time_out : 60;
			//60秒以内不可重复发送
			$times = intval(time()) - intval($data['create_time']);
			if($data && ($times < $sms_time_out)){
				return $this->ajaxError($sms_time_out.'秒内不允许重复发送');
			}
	
			//屏蔽此账号
//	        if($session_id == '1543897721000')
//	        {
//	            return array('status'=>-1,'msg'=>'发送失败');
//	        }
			if (!$telphone) {
				return $this->ajaxError("手机号不能为空");
			}  
			if (!is_numeric($telphone)) {
				return $this->ajaxError("手机号必须是数字");
			}			
			if (strlen($telphone)!=11) {
				return $this->ajaxError("手机号必须是11位的中国大陆手机号码");
			}			
			if(!preg_match("/^1[3-9]{1}\d{9}$/",$telphone)){
				return $this->ajaxError("请正确填写手机号码");
	        }
//			$ip_info = check_ip();
//			if (!$ip_info) {
//				return ajaxError('当前IP非法');
//			}
			$bing_count = model("User")->where('bing_phone',$telphone)->count();
			if ($bing_count>0) {
				return $this->ajaxError('此手机号已被绑定');
			}
			$ip = get_client_ip();
		    $count = model("VerificationCode")->where(array('ip'=>$ip, 'create_time'=>array('gt',strtotime(date('Y-m-d')))))->count();
	        if($count >= 10)
	        {
	            return $this->ajaxError('您今天发送验证码的数量已超过限额！');
	        }
	
			$row = model("VerificationCode")->insert(array('telphone'=>$telphone,'verification_code'=>$code,'create_time'=>time(), 'ip'=>$ip));
			if(!$row){
				return $this->ajaxError('验证码写入错误，发送失败');
			}
	
			$send = send_sms_reg($telphone, $code);
			list($status,$return_code,$sub_code) = $send;
	
			if($status){
				return $this->ajaxSuccess('发送成功', 1, $arr=array());
			}else if(!$status && $sub_code=='isv.BUSINESS_LIMIT_CONTROL'){
				return $this->ajaxError('您今天发送验证码的数量已超过限额！', 0, $arr=array());
			}else{
				return $this->ajaxError('发送失败,'.$sub_code, 0 ,$arr=array());
			}			
		}
	}
}
