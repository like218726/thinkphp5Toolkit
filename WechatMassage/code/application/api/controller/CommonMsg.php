<?php
namespace app\api\controller;


use app\api\common\Wechat;

use app\api\model\Device;

use app\api\model\Member;

use think\Db;

use app\api\common\CommonMessage;

class CommonMsg extends Base {
	
	/**
	 * 
	 * 忘记取眼镜
	 * 
	 */
	public function forgetGlasses() {
		if ($this->request->isGet()) {
			$member_id = input('member_id/d');
			$glass_time_seconds = input('glass_time_seconds/d');
			$glasses_type = input('glasses_type/d');
			$use_glasses_time = input('use_glasses_time/d');
			if (!$member_id) {
				return $this->ajaxError('用户ID不能为空');
			}
			if (!$glass_time_seconds) {
				return $this->ajaxError('用户洗眼镜秒数不能为空');
			}
			if (!in_array($glass_time_seconds, [1,2,3])) {
				return $this->ajaxError('用户洗眼镜秒数参数非法');
			}
			if (!$glasses_type) {
				return $this->ajaxError('用户洗眼镜类型不能为空');
			}
			if (!in_array($glasses_type, [1,2,3,4])) {
				return $this->ajaxError('用户洗眼镜类型参数非法');
			}
			if (!$use_glasses_time) {
				return $this->ajaxError('用户洗眼镜时长不能为空');
			}
			if (!in_array($use_glasses_time, [1,2,3])) {
				return $this->ajaxError('用户洗眼镜时长参数非法');
			}
			$device_id = input('device_id/d');
			if (!$device_id) {
				return $this->ajaxError('设备ID不能为空');
			}
			$device = new Device();
			$device_info = $device->getDeviceInfoById($device_id);
			if (!$device_info) {
				return $this->ajaxError("设备ID参数不合法");
			}	
			$member = new Member();
			$member_info = $member->getMemberInfoById($member_id);
			if (!$member_info) {
				return $this->ajaxError("用户ID参数非法");
			}	

			$wechat = new Wechat();
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
			
			$result = [['msg'=>CommonMessage::$_getAllMsg['112'],'time'=>CommonMessage::$_getAllNum['103']]];
			return $this->ajaxSuccess($result);
				
		}
	}
	
	/**
	 * 
	 * 根据指定的键值,在数组中是否存在,如果存在则增加对应的元素
	 * @param string $key
	 * @param array $arr
	 * 
	 */
	public function CreateNewArray($key,$arr) {
		foreach ($arr as $k=>$v) {
			if ($k == $key) {
				$arr['checked'] = $key;
			}
		}
		return $arr;
	}
}