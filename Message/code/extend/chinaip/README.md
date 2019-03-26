#### 检查当前IP是否为中国大陆IP[仅支持IP4,不支持IP6,如果需要支持IP6,请自行扩展]
此扩展主要用来短信验证进行IP过滤,如果不是中国大陆IP,则提示IP非法.

你可以这样使用:
```php
include_once('class.chinaip.php');
$chinaip = new chinaip();
if($chinaip->inChina($ip))
{
	//do something
}
```
"class.chinaip.db.update.php" 需要更新IP库,其来源于[apnic.net](http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest)

#### 编写定时任务可以用来自动更新IP库
```` php
/**
 * 
 *  更新chinaip IP4数据到本地
 */
public function ChinaipUpdate() {
	$data = file_get_contents('http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest');
	$count = preg_match_all("/apnic\|CN\|ipv4\|([0-9\.]+)\|([0-9]+)\|[0-9]+\|a.*/",$data,$array);
	for($m=array(),$i=0;$i<$count;$i++){
		$nowA = explode('.',$array[1][$i]);
		$nowA = reset($nowA)+0;
		if(!array_key_exists($nowA,$m)){
			$m[$nowA]=array();
		}
		$ipLong = ip2long($array[1][$i]);
		$ipCount = $array[2][$i];
		
		for($j=0,$find=false;$j<count($m[$nowA]);$j++){
			if($m[$nowA][$j][1]===$ipLong){
				$m[$nowA][$j][1] = $m[$nowA][$j][1]+$ipCount;
				$find = true; 
			}
		}
		if($find===false){//not found
			array_push($m[$nowA],array((int)$ipLong,(int)($ipLong+$ipCount)));
		}
	}
	$myfile = fopen(EXTEND_PATH."chinaip/class.chinaip.db", "w");
	fwrite($myfile, json_encode($m));
	fclose($myfile);
	echo 'updated !';		
}
````


