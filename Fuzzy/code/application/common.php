<?php
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
function fuzzy($data, $start=6, $end=6, $length=0, $encrypt='*') { 
	if (is_array($data)) {
		$level = arrayLevel($data); 
		$arr = array();
		if ($level == 1) {
			foreach ($data as $key=>$value) {
				$arr[$key] = strlen($value)>($start+$end) ? encryptStr($value, $start, $end, $length, $encrypt) : $value;
			}
		} else {
			foreach ($data as $key=>$value) { 
				if (is_array($value)) {
					$arr[$key] = fuzzy($value, $start, $end, $length=0, $encrypt);
				} else {
					$arr[$key] = strlen($value)>($start+$end) ? encryptStr($value, $start, $end, $length, $encrypt) : $value;
				}
			}
		}
		return $arr;
	} else {
		$temp_len = $length>0 ? $length: (strlen($data) - $start - $end); 
		if ($temp_len>0) {
			$str = $data ? substr_replace($data, str_repeat($encrypt, $temp_len), $start, $temp_len) : "";
		} else {
			$str = $data;
		}
		return $str;
	}
}

/**
 * 
 * 加密字符串
 * @param string $string 需要加密的字符串
 * @param int $start 开始位置
 * @param int $end 结尾数
 * @param int $length 替换的长度
 * @param string $encrypt 页面显示样式
 * 
 */
function encryptStr($string, $start = 6, $end = 6, $length=1, $encrypt = '*') {
	$temp_len = $length>0 ? $length: (strlen($string) - $start - $end); 
    return $string ? substr_replace($string, str_repeat($encrypt, $temp_len), $start, $temp_len) : "";
}

/**
 * 返回数组的维度
 * @param [type] $arr [description]
 * @return [type] [description]
 */
function arrayLevel($arr)
{
    $al = array(0);
    aL($arr, $al);
    return max($al);
}

function aL($arr, &$al, $level = 0)
{
    if (is_array($arr)) {
        $level++;
        $al[] = $level;
        foreach ($arr as $v) {
            aL($v, $al, $level);
        }
    }
}