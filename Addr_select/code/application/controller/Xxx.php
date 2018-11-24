<?php
namespace app\admin\controller;
class Xxx extends Base {
	public function test(){
		$address_select = $this->address_select();
		$this->assign('address_select',$address_select);  		
	}	
}	
?>