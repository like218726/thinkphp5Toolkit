<?php

namespace app\admin\model;

use think\Model;

class Address extends Model {
	protected $resultSetType = 'collection';
	
	/**
	 * 
	 * 通过地区层级来获取地址信息
	 * @param unknown_type $region_type
	 * 
	 * 
	 */
	public function getAddressInfoByRegionType($region_type) {
		if (cookie('country')) {
			$result = json_decode(cookie('country'),true);
		} else {
			$result = $this->where('region_type', $region_type)->select();
			cookie('country', $result);
		}
		return $result;
	}

	/**
	 * 
	 * 通过上一级代码查询地址信息
	 * @param unknown_type $parent_code
	 * @param unknown_type $region_type
	 * 
	 */
	public function getAddressInfoByParentCode($parent_code, $region_type) {
		if (cookie($parent_code)) {
			$result = json_decode(cookie($parent_code), true);
		} else {
			$map = array();
			$map['parent_code'] = $parent_code;
			$map['region_type'] = $region_type;
			$result = $this->where($map)->select();
			cookie($parent_code, $result);
		}
		return $result;
	}
	
	/**
	 * 
	 * 通过区域行政代码查询所属地址信息
	 * @param unknown_type $code
	 */
	public function getRegionNameByCode($code) {
		if (cookie('code')) {
			$result = cookie('code');
		} else {
			$result = $this->where('code', $code)->value('region_name');
			cookie($code, $result);
		}
		return $result;
	}	
}