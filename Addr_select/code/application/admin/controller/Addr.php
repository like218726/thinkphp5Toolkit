<?php
namespace app\admin\controller;

class Addr extends Base {
	
	/**
	 * 
	 * 省市区调用入口
	 * 
	 */
	public function index() {
		if ($this->request->isGet()) {
			$params = input(); 
			$result = $this->sel_city_page($params);
			echo json_encode($result);
			return;
		}
	}
	
	/**
	 * 
	 * 根据参数调用
	 * @param array $params
	 * 
	 */
	function sel_city_page($params){ 
		$city_code = isset($params['city_code']) ? $params['city_code'] : '';
		$level = $params['level'];
		$tag = $params['tag'];
		$is_allow_level_input = isset($params['is_allow_level_input']) ? $params['is_allow_level_input'] : '';
		$level_info = array('国家','省','市','区','镇');
		$html_str = '';
		if ($level == 1){
			$city_arr = model('Addr')->field('code,region_name')->where(['region_type'=>'1'])->select();
			$el_id = "sel_city".$tag."_v1";
			$html_str .= "<select lay-ignore id='{$el_id}' name='{$el_id}' style='width:80px;margin-right: 5px;display:inline;' onchange='sel_city_change(this)' level='1'>";
			$html_str .= "<option value=''>-{$level_info[0]}-</option>";
			foreach($city_arr as $sub){
				$html_str .= "<option value='{$sub['code']}'>{$sub['region_name']}</option>";
			}
			$html_str .= '</select>';
			foreach($level_info as $k=>$v){
				if ($k == 0){
					continue;
				}
				$_level = $k+1;
				$el_id = "sel_city".$tag."_v".$_level;
				$is_hid = $k==4 ? 'display:none;' : '';
				$html_str .= "<select lay-ignore id='{$el_id}' name='{$el_id}' style='width:80px;{$is_hid};margin-right: 5px;' onchange='sel_city_change(this)' level='{$_level}'><option value=''>-{$v}-</option></select>";
			}
			if ($is_allow_level_input>0){
				$inp_el_id = 'inp_city'.$tag;
				$html_str .= "<input style='display:none' type='text' id='{$inp_el_id}' name='{$inp_el_id}'/>";
			}
			$has_data = 1;
		}else{
			$city_arr = model('Addr')->field('code,region_name')->where(['parent_code'=>$city_code])->select();
			$has_data = empty($city_arr) ? -1 : 1;
		}
		$result = array('has_data'=>$has_data,'data'=>$city_arr,'select_html'=>$html_str,'level'=>$level,'tag'=>$tag);
		return $result;
	}		

	/**
	 * 
	 * 通过区域行政代码获取区域行政名称
	 * $code string 区域行政代码
	 * @return array
	 * 
	 */
	public function getRegionNameByCode($code) {
		$result = Db::name('address')->where(['code'=>trim($code)])->find();
		return !empty($result) ? $result['region_name']	: '';	
	}
}