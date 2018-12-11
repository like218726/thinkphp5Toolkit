<?php
header('Content-type:text/html;charset=utf-8');
$path = 'D:\music';

/**
 * 将读取到的目录以数组的形式展现出来
 * @return array
 * opendir() 函数打开一个目录句柄，可由 closedir()，readdir() 和 rewinddir() 使用。
 * is_dir() 函数检查指定的文件是否是目录。
 * readdir() 函数返回由 opendir() 打开的目录句柄中的条目。
 * @param array $files 所有的文件条目的存放数组
 * @param string $file 返回的文件条目
 * @param string $dir 文件的路径
 * @param resource $handle 打开的文件目录句柄
 */
function read_all($path)
{
    //定义一个数组
    $files = array();
    //检测是否存在文件
    if (is_dir($path)) {
        //打开目录
        if ($handle = opendir($path)) {
            //返回当前文件的条目
            while (($file = readdir($handle)) !== false) {
                //去除特殊目录
                if ($file != "." && $file != "..") {
                    //判断子目录是否还存在子目录
                    if (is_dir($path . "/" . $file)) {
                        //递归调用本函数，再次获取目录
                        $files[$file] = read_all($path . "/" . $file);
                    } else {
                        //获取目录数组
                        $file_path = $path . "/" . $file;
                        $file_path = iconv('GBK','utf-8',$file_path);
                        $file = iconv('GBK','utf-8',$file);
                        $path = iconv('GBK','utf-8',$path);
                        $file_arr['file_name'] = $file;
                        $file_arr['file_path'] = $path;
                        $files[] = $file_arr;
                    }
                }
            }
            //关闭文件夹
            closedir($handle);
            //返回文件夹数组
            return $files;
        }
    }
}

debug(read_all($path));

function debug($var, $exit = false, $method = true) {
    echo ' <pre>';
	$method ? print_r($var) : var_dump($var);
	echo '</pre> ' . '<hr style="color:red">' . '<br>';
	    
	exit;
}
