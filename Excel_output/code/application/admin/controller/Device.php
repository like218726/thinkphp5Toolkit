<?php
namespace app\admin\controller;
class Device extends Base {
	/**
	 * 
	 * 导出
	 * 
	 */
	public function outputExcel() {
		if ($this->request->isGet()) {
	        $getInfo = $this->request->get();
	         
			$where = array();
			if (!empty($getInfo['code'])) { 
				$where['code'] = $getInfo['code'];
			}
			
			if (!empty($getInfo['working_status'])) {
				$where['working_status'] = $getInfo['working_status'];
			}
			
			if (!empty($getInfo['create_time'])) {
	            $create_time =  explode(' - ', $getInfo['create_time']);
	            $start_time = trim($create_time[0]);
	            $end_time = trim($create_time[1]);
	            $where['create_time'] = ['between',[$start_time,$end_time]];			
			}

	        $data = model('Device')
					->alias('d')
					->field('d.id as id,d.code,d.device_admin,working_status,address,use_times,use_count,aalarm_count,status,d.last_time as last_use_time,d.create_time')             
			        ->where($where)
	        		->select();  
	   
	        $strTable ='<table width="500" border="1">';
	        $strTable .= '<tr>';
	
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">设备序列号</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="120">设备编号</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="180">设备管理员</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="120">设备工作状态</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">使用次数</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="120">使用人数</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">故障异常次数</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="120">状态</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="200">设备所在地</td>';
	        $strTable .= '<td style="text-align:center;font-size:12px;" width="120">最后使用时间</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="120">添加时间</td>';

	        $strTable .= '</tr>';
	
	        if(is_array($data)){
				$working_status = array('10'=>'工作','20'=>'空闲','30'=>'失效');
	        	$status = ['10'=>'启用','20'=>'禁用'];
	            foreach($data as $k=>$val){
	            	$val['working_status'] = $working_status[$val['working_status']];
	        		$val['status'] = $status[$val['status']];
	                $strTable .= '<tr>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['id'].'</td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['code'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['device_admin'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['working_status'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['use_times'].'</td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['use_count'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['aalarm_count'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['status'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['address'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['last_use_time'].' </td>';
	                $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['create_time'].' </td>';
	
	                $strTable .= '</tr>';
	            }
	        }
	
	        $strTable .='</table>';
	        unset($listinfo);
	        downloadExcel($strTable,'导出用户信息');
	        exit();	        			
		}
	}	
}	
