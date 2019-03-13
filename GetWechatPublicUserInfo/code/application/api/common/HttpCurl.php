<?php
/**
 * Created by PhpStorm.
 * User: hutuo
 * Date: 2017/7/13
 * Time: 11:01
 */

namespace app\api\common;


class HttpCurl
{
    /**
     * curl一般请求方法
     * @param  string $url 请求地址
     * @param array $data   请求数据
     * @param string $method    请求方式
     * @param string $data_type 请求的头部和数据类型
     * @return mixed|string
     */
    public static function curlRequest ( $url ,$data = [],$method = "GET",$data_type = "" )
    {
        if ( $data_type == "json" ){
            $request_data = json_encode( $data ,JSON_UNESCAPED_UNICODE );
            $header  = [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: '.strlen($request_data)
            ];
        }else{
            $header = [];
            $request_data = http_build_query($data); //够着请求数据
        }

        $ch = curl_init();													// 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url);							// 要访问的地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);						// 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_HEADER, FALSE); 							// 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT5 .0)');	// 模拟用户使用的浏览器
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 								// 设超时限置制防止死循环
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);			//设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);						//提交的数据包

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);					// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);	// 从证书中检查SSL加密算法是否存在

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	// 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

        if ( $data_type != "" ){
            curl_setopt( $ch,CURLOPT_HTTPHEADER , $header);
        }


        $tmpInfo = curl_exec($ch);	// 执行操作
        if (curl_errno($ch)) {
            return 'Error：'.curl_error($ch);
        }
        curl_close($ch);// 关闭CURL会话
        return $tmpInfo; // 返回数据(json 格式)
    }

    /*
     *模拟提交数据函数
     *requestUrl 请求的url
     *requestMethod 请求方式（post or get）
     *data 输出的数据
    */
    public  static function httpRequest($requestUrl, $requestMethod="GET", $data=""){
        $ch = curl_init();		// 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $requestUrl);	// 要访问的地址
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestMethod);	//设置请求方式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);	// 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');	// 模拟用户使用的浏览器
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	// 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	//提交的数据包
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);	// 执行操作
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
            return;
        }
        curl_close($ch);// 关闭CURL会话
        return json_decode($tmpInfo, true); // 返回数据(json 格式)
    }
}