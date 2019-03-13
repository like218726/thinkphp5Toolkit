<?php
namespace app\api\model;

use think\Model;

class VerificationCode extends Model {

	protected $resultSetType = 'collection';
	
	/**
	 * 
	 * 通过制定条件查询其信息
	 * @param array $where
	 * @return bool
	 * 
	 */
	public function getVerificationCodeByCondition($where) {
		$result = $this->where($where)->find();
		return $result;
	}
}