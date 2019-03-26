# 节假日api
    此API接口直接在controller里使用就行,具体参数说明请看模型里的方法
    1. 指定日期为节假日
    2. 指定日期为周末
    3. 指定月的周末, 节假日, 周末及节假日
    4. 指定年的周末, 节假日, 周末及节假日
    5. 指定日期为第几周,星期几,第几周星期几
    
#### 使用说明

###判断指定日期是否为节假日
``` php php
public function checkHoliday() {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->check($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	}
	$result= $api->getday($data, $delimiter);
	return ajaxSuccess($result);		
}
```

###判断指定日期时间是否为周末
```` php
public function checkWeekend ($date, $separator='-') {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->check($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	}
	$result= $api->checkweekend($data);
	return ajaxSuccess( '操作成功', 1, $result);	
}
````

###获取指定月的周末
```` php
public function getCurrentMonthWeekend($date, $separator='-') {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkMonth($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	}   
	$result= $api->checkCurrentMonthweekend($data, $delimiter='-', $status=1, $is_holiday=1);
	if ($result['code'] == 0) {
		return ajaxError('操作失败', -1, $result);
	} else {
		return ajaxSuccess( '操作成功', 1, $result); 
	}  			
}
````

###获取指定月的法定假日
```` php
public function getCurrentMonthHoliday($date, $separator='-') {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkMonth($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	} 
	$result= $api->checkCurrentMonthweekend($data, $delimiter='-', $status=1, $is_weekend=2);
	if ($result['code'] == 0) {
		return ajaxError('操作失败', -1, $result);
	} else {
		return ajaxSuccess( '操作成功', 1, $result); 
	}  			    	
}
````

###获取指定月的周末及法定假日
```` php
public function getCurrentMonthAllHoliday($date, $separator='-') {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkMonth($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	} 
	$result= $api->checkCurrentMonthweekend($data, $delimiter='-', $status=1, $is_weekend=3);
	if ($result['code'] == 0) {
		return ajaxError('操作失败', -1, $result);
	} else {
		return ajaxSuccess( '操作成功', 1, $result); 
	}  	
}
````

###获取指定年的周末
```` php
public function getCurrentYearWeekend( $date ) {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkYear( $data );
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check ); //校验日期合法性  可不校验
	} 
	$result = $api->checkCurrentYear( $data, $separator='-' , $status=1, $is_weekend=1 );
	if ($result['code'] == 0) {
		return ajaxError('操作失败', -1, $result);
	} else {
		return ajaxSuccess( '操作成功', 1, $result); 
	}      	
}    
````

###获取指定年的法定假日
```` php
public function getCurrentYearHoliday( $date ) {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkYear( $data );
	if($check['status']==0) {
		return ajaxError( '操作失败', 0, $check );//校验日期合法性  可不校验
	} 
	$result= $api->checkCurrentYear($data, $separator='-' , $status=1, $is_weekend=2 );
	if ($result['code'] == 0) {
		return ajaxError( '操作失败', 0, $result['msg'] );
	} else {
		return ajaxSuccess( '操作成功', 1, $result); 
	}  	
}
````

###获取指定年的周末及法定假日
```` php
public function getCurrentYearAllHoliday( $date ) {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->checkYear( $data );
	if($check['status']==0) {
		return ajaxSuccess($check);//校验日期合法性  可不校验
	} 
	$result= $api->checkCurrentYear($data, $separator='-' , $status=1, $is_weekend=3);
	if ($result['code'] == 0) {
		return ajaxError( '操作失败', 0, $result['msg'] );
	} else {
		return ajaxSuccess( '操作成功', 1, $result);   
	}      	
}
````

###判断指定天是否为第几周
```` php
public function getWeek() {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->check($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxSuccess($check);//校验日期合法性  可不校验
	}  
	$result= $api->getWeekNum($data, $delimiter); 
	return ajaxSuccess( '操作成功', 1, $result);		  	
}
````

###判断指定天为星期几
```` php
public function getWeekst() {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->check($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxSuccess($check);//校验日期合法性  可不校验
	}  
	$result= $api->getWeekst( $data ); 
	return ajaxSuccess( '操作成功', 1, $result);	   	
}
````

###判断指定天为第几周星期几
```` php
public function getWeekInfo () {
	Loader::import('dateapi.include.dateapi', EXTEND_PATH, '.class.php');  
	$data= input('get.date');
	$api= new \dateapi();
	$check=$api->check($data, $delimiter='-');
	if($check['status']==0) {
		return ajaxSuccess($check);//校验日期合法性  可不校验
	}  
	$result= $api->getWeekInfo( $data ); 
	return ajaxSuccess( '操作成功', 1, $result);	      	
}
````