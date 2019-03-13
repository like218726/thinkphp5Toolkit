<?php

namespace app\admin\controller;

use think\Controller;

class Liandong extends Controller {
	
	public function index() {
		if ($this->request->isGet()) {			
			$template = '';
			$data = array();
			if (config('LINK_AGE') == 2) {
				$template = 'index2';
				$map = array();
				$map['parent_code'] = 1;
				$map['region_type'] = 2;
				$data = model('Address')->where($map)->select()->toArray();		
				//省份
				$province = model('Address')->where('region_type', 2)->where('parent_code', $row['country_code'])->select()->toArray();	
				$this->assign('province', $province);
				//城市
				$city = model('Address')->where('region_type', 3)->where('parent_code', $row['province_code'])->select()->toArray();	
				$this->assign('city', $city);						
			}else if (config('LINK_AGE') == 3) {
				$template = 'index3';
				$map = array();
				$map['parent_code'] = 1;
				$map['region_type'] = 2;
				$data = model('Address')->where($map)->select()->toArray();
				//省份
				$province = model('Address')->where('region_type', 2)->where('parent_code', $row['country_code'])->select()->toArray();	
				$this->assign('province', $province);
				//城市
				$city = model('Address')->where('region_type', 3)->where('parent_code', $row['province_code'])->select()->toArray();	
				$this->assign('city', $city);
				//地区
				$area = model('Address')->where('region_type', 4)->where('parent_code', $row['city_code'])->select()->toArray();	
				$this->assign('area', $area);					
			} else if (config('LINK_AGE') == 4){
				$map = array();
				$map['region_type'] = 1;
				$data = model('Address')->where($map)->select()->toArray();
				//国家
				$country = model('Address')->where('region_type', 1)->select()->toArray();	
				$this->assign('country', $country);
				//省份
				$province = model('Address')->where('region_type', 2)->where('parent_code', $row['country_code'])->select()->toArray();	
				$this->assign('province', $province);
				//城市
				$city = model('Address')->where('region_type', 3)->where('parent_code', $row['province_code'])->select()->toArray();	
				$this->assign('city', $city);
				//地区
				$area = model('Address')->where('region_type', 4)->where('parent_code', $row['city_code'])->select()->toArray();	
				$this->assign('area', $area);				
				$template = 'index4';
			} else if (config('LINK_AGE') == 5) {
				$map = array();
				$map['region_type'] = 1;
				$data = model('Address')->where($map)->select()->toArray();		
				//国家
				$country = model('Address')->where('region_type', 1)->select()->toArray();	
				$this->assign('country', $country);
				//省份
				$province = model('Address')->where('region_type', 2)->where('parent_code', $row['country_code'])->select()->toArray();	
				$this->assign('province', $province);
				//城市
				$city = model('Address')->where('region_type', 3)->where('parent_code', $row['province_code'])->select()->toArray();	
				$this->assign('city', $city);
				//地区
				$area = model('Address')->where('region_type', 4)->where('parent_code', $row['city_code'])->select()->toArray();	
				$this->assign('area', $area);	
				//乡
				$town = model('Address')->where('region_type', 5)->where('parent_code', $row['area_code'])->select()->toArray();	
				$this->assign('town', $town);											
				$template = 'index5';
			}
			$this->assign('data', $data);		
			return $this->fetch($template);
		}
	}
}