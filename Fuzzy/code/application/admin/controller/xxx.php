<?php
use app\admin\controller\Base;
class xxx extends Base {
	
	/**
	 * 
	 * 当字符串小于12位时,不加密,如果要小于12位还要加密
	 * @param string $string 需要加密的字符串
	 * @param int $start 开始位置
	 * @param int $end 结尾数
	 * @param int $length 替换的长度
	 * @param string $encrypt 页面显示样式
	 * $str = '123456';
	 * encryptStr($str,1,1,1)
	 * 
	 */
	public function encryptStr() {
		if ($this->request->isGet()) {
			$str = 'abcedfghijklmnopqrstuvwxyz0123456789';
			$result = encryptStr($str);
		}
	}
	
	/**
	 * 
	 * 模糊化
	 * @param mixed $data 数组(一维,二维,多维)或者字符串[默认情况下要模糊化的值必须大于12位]
	 * @param int $start 开始位置
	 * @param int $end 结束几位
	 * @param int $length 要模糊化位数
	 * @param int $encrypt 模糊化样式
	 * @return mixed 数组或者字符串
	 * 
	 */
	public function fuzzy() {
		$data = array(
			'111'=>array('a'=>'12345678963258741','b'=>'abcdefghijklmnopqrstuvwxyz'),
			'222'=>array('a'=>'12345678963258741','b'=>'abcdefghijklmnopqrstuvwxyz'),
		);
		$result = fuzzy($data);
	}
}