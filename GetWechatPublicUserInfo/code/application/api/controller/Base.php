<?php
namespace app\api\controller;

use think\Request;

use app\api\common\Wechat;

use think\Controller;

use think\Cache;

class Base extends Controller
{
    //设计师id
    protected $_cookie_member_id = 0;

    protected $_cookie_device_id = 0;
    
    public function _initialize()
    {
        parent::_initialize();

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS');
        header("Access-Control-Allow-Headers:x-foo, x-requested-with, Content-Type, MUserAgent, MToken, UID");
		
        if (Cookie::has('member_id')) { 
        	$this->_cookie_member_id = Cookie::get('member_id');        	
        } else { 
            $wechat = new Wechat();
       
	        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
				//微信oAuth认证
				$reurl = urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				$redirect_url = "http://" . $_SERVER['HTTP_HOST'] . "/Oauth?reurl=" . $reurl;
				$wechat->oAuth($redirect_url);
	        }      	
        }

		if ($this->request->param('device_id', '', 'trim')) {
			$this->_cookie_device_id = input('device_id/d'); 
		}     
		
		if(empty($this::$_NotNeedCheckToken))
        {
            //校验token合法性
            $header = Request::instance()->header();

            if(empty($header['token']))
            {
                $this->ajaxError('TOKEN信息不存在', -901)->send();
                exit;
            }
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            	$accessToken = Cache::get("TOKEN_member_".$this->_cookie_member_id);           	
            } else { 
            	$accessToken = Cache::get("TOKEN_device_".$this->_cookie_device_id);
            }
        	
            if(empty($accessToken))
	        {
	           $this->ajaxError('请获取TOKEN信息', -902)->send();
	           exit();
	       } 		
           if($accessToken != $header['token'])
           {
               $this->ajaxError('TOKEN失效，请重新获取：服务端token:'.$accessToken. " header Token:".$header['token'], -903)->send();
               exit();
           }
        }
    }    
    
    /**
     * Ajax正确返回，自动添加debug数据
     * @param $msg
     * @param array $data
     * @param int $code
     */
    public function ajaxSuccess( $msg, $code = 1, $data = array() ){
        $returnData = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        if( !empty($this->debug) ){
            $returnData['debug'] = $this->debug;
        }
        return json($returnData);
    }

    /**
     * Ajax错误返回，自动添加debug数据
     * @param $msg
     * @param array $data
     * @param int $code
     */
    public function ajaxError( $msg, $code = 0, $data = array() ){
        $returnData = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        if( !empty($this->debug) ){
            $returnData['debug'] = $this->debug;
        }

        return json($returnData);
    }     
}