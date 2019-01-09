<?php
namespace app\admin\controller;
class Xxx extends Base {
	public function test(){
		//方法一:
		$address_select = $this->address_select();
		$this->assign('addr_select',$address_select); 
		//方法二: 直接用模板代码 		
	}	
}	
?>