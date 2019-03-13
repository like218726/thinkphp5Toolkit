<?php
namespace app\admin\controller;

class AdManage extends Base{
	/**
	 * 
	 * 获取设备列表
	 * 
	 */
	public function getDeviceList() {
		if ($this->request->isGet()) {
		    $ids = input('get.ids/s');

	        $musics = "";
	        if(!empty($ids))
	        {
	            $music_arr = [];
	            $lists = explode(',', $ids);
	            foreach ($lists as $val)
	            {
	                $music_arr[] = (int)$val;
	            }
	            $musics = json_encode($music_arr);
	        }	        
			return $this->fetch("ad_manage/getDeviceList",['musics'=>$musics]);
		}
	}
	
	/**
	 * 
	 * 获取设备列表
	 * 
	 */
	public function ajaxGetDevice() {
		if ($this->request->isPost()) { 
	        $postData = input();  
	        $start = $postData['start'] ? $postData['start'] : 0;
	        $limit = $postData['length'] ? $postData['length'] : 20;
	        $draw = $postData['draw'];
	        $getInfo = $this->request->get();
	      
			$where = array();
			if (!empty($getInfo['code'])) { 
				$where['code'] = ['like','%'.$getInfo['code'].'%'];
			}
			
			if (!empty($getInfo['device_admin'])) {
				$where['device_admin'] = ['like','%'.$getInfo['device_admin'].'%'];
			}
			
			if (!empty($getInfo['address'])) {
				$where['address'] = ['like','%'.$getInfo['address'].'%'];
			}
			
	        $total = model("Device")->alias('d')->where($where)->count();
	        
	        $info = model('Device')->alias('d')
					->field('d.id as id,d.code,d.device_admin,working_status,address,use_times,use_count,aalarm_count,status,d.last_time as last_use_time,d.create_time')             
			        ->where($where)->limit($start, $limit)->select();  
	     
	        $working_status = array('10'=>'工作','20'=>'空闲','30'=>'失效');
	        $status = ['10'=>'启用','20'=>'禁用'];
	        $list = array();	
	        foreach ($info as $key=>$value) {
	        	$value['working_status'] = $working_status[$value['working_status']];
	        	$value['status'] = $status[$value['status']];
	        	$list[$key] = $value;
	        }		
	        		
	        $data = array(
	            'draw'            => $draw,
	            'recordsTotal'    => $total,
	            'recordsFiltered' => $total,
	            'data'            => $list
	        );
	        $this->ajaxReturn($data, 'json');				
		}
		
	}
	
	/**
	 * 
	 * 查询设备
	 * 
	 */
	public function DeviceDetail() {
		if ($this->request->isGet()) { 
			$device_code = input('device_code');
			$musics = "";
	        if(!empty($device_code))
	        {
	            $musics = model('Device')->where(" code in ($device_code)")->select(); 
	        }	
			return $this->fetch("ad_manage/DeviceDetail",['list'=>$musics]);
		}
	}	
}