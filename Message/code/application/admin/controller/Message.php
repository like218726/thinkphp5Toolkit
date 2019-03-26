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
			
			$alicloud = config('ALICLOUD');	
			
			$code = rand(100000, 999999);
			$telphone = input('post.telphone','','trim');
			
			//1. 手机号不能为空
			if (!$telphone) {
				return ajaxError("手机号不能为空");
			}  			
			
			//2. 手机号满足130-199的手机号规则
			if(!preg_match("/^1[3-9]{1}\d{9}$/",$telphone)){
				return ajaxError("请正确填写手机号码");
	        }			
			
	        //10分钟内不允许重复发送
			$map = array(
				'telphone' => $telphone,
				'verification_code' => $code,
				'is_used' => 0,
				'create_time' => array('gt', time() - $alicloud['valid_minute'] * 60),
	 		);
			$data = model("VerificationCode")->where($map)->find();			
			
			if ($data) {
				return ajaxError($alicloud['valid_minute'].'分钟内不允许重复发送');
			}
			
			//4. 已用户绑定的手机号不能发送
			if($needCheck == true)
			{
				$user = model('User')->get(['mobile'=>$telphone,'openid'=>['<>', '']]); 
		        if(!empty($user))
		        {
		            return ajaxError('手机号码已注册');
		        }	
			}		    

	        //5. 只能在中国大陆的手机才能发送验证码
	        Loader::import('chinaip.chinaip', EXTEND_PATH, '.class.php');  
	        $chinaip = new \chinaip();
	        if(!$chinaip->inChina()) {
	        	return ajaxError('当前IP非法');
	        }
			
			//6.同一IP在当天之内不能超过10次
			$ip = get_client_ip();
			$count = model("VerificationCode")->where(array('ip'=>$ip, 'create_time'=>array('gt',strtotime(date('Y-m-d')))))->count();
	        if($count > 10)
	        {
	            return ajaxError('您今天发送验证码的数量已超过10次！');
	        }			
	
			//调用阿里云短信接口成功后,插入到验证码数据库
	        $send = send_sms($telphone, $code, $sms_cfg_var='code', $template_code=$alicloud['TemplateCode']);
			list($status,$return_code,$sub_code) = $send;
	        if($status){
	        	model("VerificationCode")->insert(array('telphone' => $telphone, 'verification_code' => $code, 'create_time' => time(), 'ip'=>get_client_ip()));
				return ajaxSuccess('发送成功', 1, $arr=array());
			}else if(!$status && $sub_code=='isv.BUSINESS_LIMIT_CONTROL'){
				return ajaxError('您今天发送验证码的数量已超过限额！', 0, $arr=array());
			}else{
				return ajaxError('发送失败,'.$sub_code, 0 ,$arr=array());
			}			
		}
	}
}
