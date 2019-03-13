<?php
/**
 * Created by PhpStorm.
 * User: lijunhua
 * Date: 2019/1/21
 * Time: 15:10
 */

namespace app\api\controller;

use think\Cookie;

use think\Cache;
use think\Controller;
use think\Request;


class Base extends Controller
{
    protected $_uid = '';
    protected $_cookie_openid = '';

    public function _initialize(){
        $header = Request::instance()->header();

        if(empty($header['uid']))
        {
            ajaxError('请登录', 41008)->send();
            exit;
        }else{
            $this->_uid = $header['uid'];
        }

        if (Cookie::has('openid')) {
        	$this->_cookie_openid = Cookie::get('openid');
        } else {
        	$this->_cookie_openid = model('User')->where(array('id'=>$this->_uid))->value('openid');
        }
        
        //校验token合法性
        if(empty($header['token']))
        {
            ajaxError('TOKEN信息不存在', -901)->send();
            exit;
        }

        $accessToken = Cache::get("TOKEN_".$this->_uid);

        if(empty($accessToken))
        {
            ajaxError('请获取TOKEN信息', -902)->send();
            exit;
        }

        if($accessToken != $header['token'])
        {
            ajaxError('TOKEN失效，请重新获取：服务端token:'.$accessToken. " header Token:".$header['token'], -903)->send();
            exit;
        }

    }
}