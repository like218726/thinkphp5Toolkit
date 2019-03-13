<?php
namespace app\api\controller;

use app\api\common\Wechat;

use app\api\model\Member;

class CommonMsg extends Base {
	
	/**
	 * 
	 * 忘记取眼镜
	 * 
	 */
	public function forgetGlasses() {
		if ($this->request->isGet()) {

			$wechat = new Wechat();
			$member_info = model('Member')->getMemberInfoById($member_id=1);
			$openid = $member_info['openId'];
            $data = array(
				"touser" => $openid,
				"template_id" => "191DbXYebZ0xNxbL-9UgARag7dRE54lVNDbBE2APUj0",
				"topcolor" => "#FF0000",
				"url"         => "",
				"data" => array(
					"first" => array("value" => "尊敬的".$member_info['nickname']."用户，您有新的报警通知:","color" => "#173177"),
					"keyword1" => array("value" => $device_info['code'],"color" => "#173177"),
					"keyword2" => array("value" => CommonMessage::$_glassType[$glasses_type],"color" => "#173177"),
					"keyword3" => array("value" => date('Y-m-d H:i:s',time()),"color" => "#173177"),
					"keyword4" => array("value" => CommonMessage::$_getAllMsg['114'],"color" => "#FF0000"),
					"remark" => array("value" => "如您在使用过程中遇到任何疑问，欢迎拨打我们的客服电话18014682315","color" => "#173177")
				)
			);
			$wechat->sendTemplateMsg($data);		
				
		}
	}
}