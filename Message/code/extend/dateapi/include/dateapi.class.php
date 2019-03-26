<?php
/**
 * 精简节假日api
 * @copyright tool.bitefu.net
 * @auther xiaogg@sina.cn
 */
class dateapi {
    public $path='dateapi/data/';//数据目录
    public $_msg = ['0'=>'工作日', '1'=>'周末', '2'=>'法定假日'];
    
    //获取一天的节假日情况
    /**
     * 
     * 返回指定日期的节假日情况
     * @param unknown_type $day
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     */
	public function getday($day, $delimiter){
	    $year=substr($day,0,4);
	    $returndata=0;
        $yeardata= $this->yeardate($year);   
        if($yeardata){ 
            $dayArr=explode($delimiter, $day); 
            $dayvalue=@$yeardata[$dayArr['1'].$dayArr['2']]; 
            if(!empty($dayvalue) || is_numeric($dayvalue)) {
            	$returndata= $dayvalue;
            } else {
            	$returndata=$this->checkwork($day);
            }
        }
        $return=array('info'=>$day, 'status'=>$returndata, 'msg'=>$this->_msg[$returndata]);
        return $return;
	}

    /**
     * 
     * 检查是否为周末
     * @param unknown_type $day
     */
	private function checkwork($day){
	   $weak= date("N",strtotime($day)); 
       return in_array($weak,array(6,7)) ? 1 : 0;
	}
	
	/**
	 * 
	 * 判断指定日期为第几周
	 * @param unknown_type $date
	 */
	public function getWeekNum ( $date ) {
		return ['date'=>$date,'week_num'=>date( 'W', strtotime($date) )];
	}
	
	/**
	 * 
	 * 判断指定日期为星期几
	 * @param unknown_type $date
	 * @return 1:星期一,7:星期天
	 */
	public function getWeekst ( $date ) {
		return ['date'=>$date,'week_day'=>date( 'N', strtotime($date) )];
	}	
	
	/**
	 * 
	 * 判断指定日期为第几周,星期几
	 * @param unknown_type $date
	 */
	public function getWeekInfo( $date ) {
		$data = [];
		$data['date'] = $date;
		$data['week_num'] = date( 'W', strtotime($date) );
		$data['week_day'] = date( 'N', strtotime($date) );		
		return $data;
	}
		
	/**
	 * 
	 * 判断指定日期是否为周末,不考虑法定假日
	 * @param unknown_type $day
	 */
	public function checkweekend ( $day ) {
		$status = $this->checkwork($day);
		return ['info'=>$day,'status'=>$status, 'msg'=>$this->_msg[$status]];
	}
	
    /**
     * 
     * 检查是否为合法日期
     * @param unknown_type $day 指定日期
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     */
    public function check($day, $delimiter = ''){
    	if(empty($day) || strlen($day)<4){
    		$return['info'] = '日期参数不正确';
    		$return['status'] = 0;
    		return $return; 
    	} 
		
    	$day_time=strtotime($day);
    	if(date('Y'.$delimiter.'m'.$delimiter.'d',$day_time)!=$day){
    		$return['info'] = '日期格式错误';
    		$return['status'] = 0;
    	} else {
    		$return['status'] = 1;
    		$return['info'] = '';
    	}
        return $return;
    }
    
    /**
     * 
     * 检查是否为合法年月
     * @param unknown_type $date
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     */
    public function checkMonth($date, $delimiter = '') {
    	if(empty($date) || strlen($date)<4){
    		$return['info'] = '年月参数不正确';
    		$return['status'] = 0;
    		return $return; 
    	}  
    	
    	$day_time=strtotime($date);
    	if(date('Y'.$delimiter.'m',$day_time)!=$date){
    		$return['info'] = '年月格式错误';
    		$return['status'] = 0;
    	} else {
    		$return['status'] = 1;
    		$return['info'] = '';
    	}
        return $return;    	   	
    }
    
    /**
     * 
     * 检查是否为合法年
     * @param unknown_type $date
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     */
    public function checkYear( $date ) {
    	if( empty($date) || strlen($date)<4 ){
    		$return['info'] = '年参数不正确';
    		$return['status'] = 0;
    		return $return; 
    	}  
    	
    	$day_time=strtotime($date);
    	if( date('Y',$day_time)!=$date ){
    		$return['info'] = '年格式错误';
    		$return['status'] = 0;
    	} else {
    		$return['status'] = 1;
    		$return['info'] = '';
    	}
        return $return;    	    	
    }
    
    /**
     * 
     * 获取指定年月的周末,法定假日,周末及法定假日
     * @param unknown_type $date
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     * @param unknown_type $status 是否返回状态 0:默认不返回状态,1返回状态
     * @param unknown_type $is_holiday 1:周末,2:法定假日,3:周末及法定假日
     */
    public function checkCurrentMonthweekend($date, $delimiter = '', $status=0, $is_holiday=0) {
    	$year = "";
    	$month = "";
    	if(empty($date)) {
    		$year = date('Y');
    		$month = date('m');
    	} else {
    		$year_arr = explode($delimiter, $date);
    		$year = $year_arr['0'];
    		$month = $year_arr['1'];
    	} 
    	if (!in_array($delimiter, ['','-','/'])) {
    		return ['code'=>'0', 'msg'=>'分隔符参数不合法,只能传入-或者/或者默认不填写'];
    	}
        if (!in_array($status, ['0','1'])) {
    		return ['code'=>'0', 'msg'=>'返回状态参数不合法,只能传入0或者1'];
    	}
    	if (!in_array($is_holiday, [1,2,3])) {
    		return ['code'=>'0', 'msg'=>'假日状态参数不合法,只能传入1或者2或者3'];
    	}    	
    	$new_file= EXTEND_PATH.$this->path.$year.'_data.json';  
    	$return_data = [];
        if(file_exists($new_file)){ 
            $nowdata=json_decode( file_get_contents($new_file),true ); 
            if ( !$nowdata ) {
            	return ['code'=>'0', 'msg'=>''];
            } 
            $month_data = [];
            foreach ( $nowdata as $k=>$v ) {
            	$tmp = substr($k, 0, 2); 
                if ($delimiter) {
            		$k = substr($k, 0, 2).$delimiter.substr($k, 2, 2);
            	}            	
            	if ( $status == 0 ) {
            		if (in_array( $is_holiday, [1,2]) ) {
		            	if ( $tmp == $month && $v == $is_holiday ) {
		            		array_push($month_data, $year.$delimiter.$k);
		            	}
            		} elseif ($is_holiday == 3) {
            			if ( $tmp == $month ) {
            				array_push( $month_data, $year.$delimiter.$k );
            			}
            		}
            	} else {
            		if ( in_array($is_holiday, [1,2]) ) {
	            		if ( $tmp == $month && $v == $is_holiday ) {
	            			$month_data[$year.$k]['month'] = $year.$delimiter.$k;
	            			$month_data[$year.$k]['status'] = $v;
	            		}
            		} elseif ($is_holiday == 3) {
            			if ( $tmp == $month ) {
	            			$month_data[$year.$k]['month'] = $year.$delimiter.$k;
	            			$month_data[$year.$k]['status'] = $v;  
            			}          			
            		}
            	}
            }
            $return_data['date'] = $date;
            $return_data['info'] = $month_data; 
        } else {
        	$return_data['date'] = $date;
        	$return_data['info'] = [];
        }
        return ['code'=>1,'msg'=>$return_data];  
    }

    /**
     * 
     * 获取当年的周末,法定假日,周末及法定假日
     * @param unknown_type $year
     * @param unknown_type $status 是否返回状态 0:默认不返回状态,1返回状态
     * @param unknown_type $is_weekend 1:周末,2:法定假日,3:周末及法定假日
     * @param unknown_type $delimiter 默认为空, 分隔符可以填写-,/
     */
    public function checkCurrentYear( $year, $delimiter="", $status="0", $is_holiday="1" ) {
    	if(empty($year)) {
    		$year=date('Y');
    	} 
    	if (!in_array($status, ['0','1'])) {
    		return ['code'=>'0', 'msg'=>'返回状态参数不合法,只能传入0或者1'];
    	}
    	if (!in_array($is_holiday, [1,2,3])) {
    		return ['code'=>'0', 'msg'=>'假日状态参数不合法,只能传入1或者2或者3'];
    	}
    	$new_file= EXTEND_PATH.$this->path.$year.'_data.json';  
    	$return_data = [];
        if(file_exists($new_file)){ 
            $nowdata=json_decode( file_get_contents($new_file),true );   

       	 	if ( !$nowdata ) {
            	return ['code'=>'0', 'msg'=>''];
            }
            foreach ( $nowdata as $k=>$v ) {
            	if ($delimiter) {
            		$k = substr($k, 0, 2).$delimiter.substr($k, 2, 2);
            	}
            	if ( $status == 0 ) {
            		if (in_array( $is_holiday, [1,2]) && $v == $is_holiday ) {
            			array_push($return_data, $year.$delimiter.$k); 
            		} elseif ( $is_holiday == 3 ) {
            			array_push($return_data, $year.$delimiter.$k);
            		}
            	} else {
            		if (in_array( $is_holiday, [1,2]) && $v == $is_holiday ) {
            			$return_data[$year.$k]['date'] = $year.$delimiter.$k;
            			$return_data[$year.$k]['status'] = $is_holiday;          			
            		}elseif ( $is_holiday == 3 ) {
             			$return_data[$year.$k]['date'] = $year.$delimiter.$k;
            			$return_data[$year.$k]['status'] = $v;              			
            		}
            	} 
            }
        } else { 
            $return_data = [];
        } 
        return ['code'=>1,'msg'=>$return_data];   	
    }
    
    //获取一年的数据
    private function yeardate($year=''){
        if(empty($year))$year=date('Y');
        $new_file= EXTEND_PATH.$this->path.$year.'_data.json';  
        
        $nowdata = [];
        if(file_exists($new_file)){ 
            $nowdata=json_decode(file_get_contents($new_file),true);  
            return $nowdata;
        } else { 
            return $nowdata;
        }
    }
}
?>