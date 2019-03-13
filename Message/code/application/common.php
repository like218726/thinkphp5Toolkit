<?php
/**
 * 
 * 判断是否为国内IP
 * return bool
 * 
 */
function check_ip () {
	ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; GreenBrowser)');	
	if(getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if(getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if(getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
	}else{
		$ip = "NULL";
	}
	
	if ($ip == '127.0.0.1') {
		return true;
	} else {
		$url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
		$resp = file_get_contents($url);
		if(!empty($resp))
		{
			$info = json_decode($resp, true);
			if($info['data']['country'] != '中国')
			{
				return false;
			} else {
				return true;
			}
		}	
	}
}

/**
  * 发送验证码短信
  * @param $mobile  手机号码
  * @param $code    验证码
  * @return bool    短信发送成功返回true失败返回false
  *
  * 验证码模板：${product}用户注册验证码：${code}。请勿将验证码告知他人并确认该申请是您本人操作！
  */
function send_sms_reg( $mobile, $code)
{ 
	$alicloud = config('ALICLOUD');	
	$sms_template_code = $alicloud['TemplateCode']; //模板编号
	$sign_name = $alicloud['SignName']; //签名名称
	if(empty($sms_template_code) || empty($sign_name)){
		return false;
	}
	$product = $alicloud['sms_product'];        
	// 短信模板参数拼接字符串,如果模板里有$product变量就开启82行,反之83号
//	$sms_cfg = json_encode(array('code'=>$code,'product'=>$product));  
	$sms_cfg = json_encode(array('code'=>$code));  
	// 发送验证码短信
	$sms_send = sendSms($mobile, $sms_template_code, $sms_cfg, $sign_name);
	$success = $sms_send->Code == 'OK' ? true : false; //成功标识
	$return_code = $sms_send->Code; //返回的编码
	$sub_code = $sms_send->Message; //错误码

	if ($success)
	{
		$db = model("VerificationCode"); 
		$map = array(
			'telphone' => $mobile,
			'verification_code' => $code,
			'is_used' => 0,
			'create_time' => array('gt', time() - 10 * 60),
 		);
		$data = $db->where($map)->find();

		// 没有就插入验证码,供验证用
		empty($data) && $db->insert(array('telphone' => $mobile, 'verification_code' => $code, 'create_time' => time(), 'ip'=>get_client_ip()));
		return array(true,'','');
	} else {
		return array(false,$return_code,$sub_code);
	}
}

/**
 * 
 * 发送短信
 * @param unknown_type $mobile
 * @param unknown_type $sms_template_code
 * @param unknown_type $param_str
 * @param unknown_type $sign_name
 */
function sendSms($mobile, $sms_template_code, $param_str, $sign_name='注册验证')
{
	vendor('dysms.Sms');
	$sms = new Sms();

	$alicloud = config('ALICLOUD');	
	$sms->appkey = $alicloud['AccessKeyId'];
	$sms->secretKey = $alicloud['AccessKeySecret'];

	$res = $sms->sendSms($mobile, $sms_template_code, $param_str, $sign_name);
	return $res;
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