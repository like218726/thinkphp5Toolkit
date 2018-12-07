<?php
namespace app\api\controller;
use app\api\common\Wechat;

class JsSdk extends Base {
	
	/**
	 * 参数入口
	 */
	public function getJsSdkParam() {
		$weChat = new Wechat();
        $signPackage =  $weChat -> getSignPackage();
        
        return $this->ajaxSuccess($signPackage);
	}
} 

