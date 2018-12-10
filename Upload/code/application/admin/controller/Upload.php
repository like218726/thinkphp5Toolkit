<?php
namespace app\admin\controller;

class Upload extends Base{
	
	/**
	 * 
	 * 单图上传
	 * 
	 */
    public function index()
    {
    	// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('file');  //不能用image,如果用了的话,validate方法不能识别,非常奇怪
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $file->validate(['size'=>52428800,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'Uploads');
		if($info){
			// 成功上传后 获取上传信息		
            $res = [
                'type'=>1,
                'msg'=>'success',
                'image_path'=> 'Uploads/'.str_replace("\\", "/", $info->getSaveName()),
            ];
            echo json_encode($res);						
			
		}else{
			$res = [
                'type'=>0,
                'msg'=>$file->getError()
            ];
             echo json_encode($res);	
		}
        
    }
    
    /**
     * 
     * 视频上传
     * 
     */
    public function video() {
    	$file = request()->file('file');    	
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $file->validate(['size'=>52428800,'ext'=>'mp4'])->move(ROOT_PATH . 'public' . DS . 'Uploads');

		if($info){ 							
			// 成功上传后 获取上传信息		
            $res = [
                'type'=>1,
                'msg'=>'success',
                'image_path'=> 'Uploads/'.str_replace("\\", "/", $info->getSaveName()),
            ];
            echo json_encode($res);									
		}else{
			$res = [
                'type'=>0,
                'msg'=>$file->getError()
            ];
             echo json_encode($res);	
		}    	
    }
}