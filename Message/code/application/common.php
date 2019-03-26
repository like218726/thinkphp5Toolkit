<?php


/**
  * 发送验证码短信
  * @param $mobile  手机号码
  * @param $var    替换短信内容的变量
  * @param $template_code 模板编号
  * @param $$sms_cfg_var 模板变量参数 code为验证码模板的变量,time为会员卡过期模板的变量
  * @example 短信验证码 send_msg('18620888755', '648654', 'code', 'SMS_159490493')
  * 		  会员卡过期 send_msg('18620888755', '2019-03-22-11:22:36', 'time', 'SMS_160860272')
  * @return bool    短信发送成功返回true失败返回false
  *
  * 验证码模板：${product}用户注册验证码：${code}。请勿将验证码告知他人并确认该申请是您本人操作！
  */
function send_sms( $mobile, $var, $sms_cfg_var='code', $template_code='')
{ 
	$alicloud = config('ALICLOUD');	
	$sign_name = $alicloud['SignName']; //签名名称
	if(empty($template_code) || empty($sign_name)){
		return array(false, '-1103', '模板编号或者签名为空');
	} 
	$product = $alicloud['sms_product'];        
	// 短信模板参数拼接字符串,如果模板里有$product变量就开启82行,反之83号
//	$sms_cfg = json_encode(array('code'=>$code,'product'=>$product));  
	$sms_cfg = json_encode(array($sms_cfg_var=>$var));  
	// 发送验证码短信
	vendor('dysms.Sms');
	$sms = new Sms();

	$sms->appkey = $alicloud['AccessKeyId'];
	$sms->secretKey = $alicloud['AccessKeySecret'];

	$sms_send = $sms->sendSms($mobile, $template_code, $sms_cfg, $sign_name);
		
	$success = $sms_send->Code == 'OK' ? true : false; //成功标识
	$return_code = $sms_send->Code; //返回的编码
	$sub_code = $sms_send->Message; //错误码

	if ($success)
	{
		//将短信内容插入到数据库需要在调用此方法内实现
		return array(true,'','');
	} else {
		return array(false,$return_code,$sub_code);
	}
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip() {
	$ip = '';
	if (isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP']) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		$allIps = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$allIpsArray = explode(',', $allIps);
		$ip = $allIpsArray[0];
	} else if (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if (empty($ip)) {
		$ip = '0.0.0.0';
	}
	return $ip;
}