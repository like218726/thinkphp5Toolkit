<?php
namespace app\admin\controller;

use think\Controller;

class Address extends Controller {
	
	/**
	 * 
	 * 联动查询
	 * 
	 */
	public function index() {
		if ($this->request->isPost()) {
			$code = input('post.code', '', 'trim');
			$region_type = input('post.region_type', '0', 'trim');
			$map = array();
			$map['parent_code'] = $code;
			$map['region_type'] = $region_type;
			$result = model('Address')->where($map)->select()->toArray();
			
			if (!$result) {
				return ajaxError("没有数据", -1, array());
			} else {
				return ajaxSuccess("操作成功", 1, $result);
			}			
			
		}
	}
	

}