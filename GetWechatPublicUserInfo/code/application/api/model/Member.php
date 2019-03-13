<?php
namespace app\api\model;

use think\Model;

class Member extends Model {
	protected $resultSetType = 'collection';
	
	/**
	 * 
	 * 通过成员ID获取其信息
	 * @param string $member_id
	 * @return Array
	 * 
	 */
	public function getMemberInfoById($member_id) {
		$result = $this->where(['id'=>$member_id])->find();
		return $result;
	}
	
	/**
	 * 
	 * 通过openid查询是否存在
	 * @param string $openid
	 * @return array 
	 * 
	 */
	public function getMemberInfoByOpenid($openid) {
		$result = $this->where(['openId'=>$openid])->find();
		return $result;
	}
	
	/**
	 * 
	 * 保存用户
	 * @param unknown_type $data
	 */
	public function SaveMember($data) {
		$this->insert($data);
	}
}