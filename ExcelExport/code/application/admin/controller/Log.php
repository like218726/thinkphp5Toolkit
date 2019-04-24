<?php

namespace app\admin\controller;

class Log extends Base {
    /**
     * 
     * 导出
     * 
     */
    public function export(){
    	
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);    	
    	
        $getInfo = input('get.');
        $where = [];
        if (!empty($getInfo['add_time'])) {
            $create_time =  explode(' - ', $getInfo['add_time']);
            $start_time = strtotime(trim($create_time[0]));
            $end_time = strtotime(trim($create_time[1]));
            $where['addTime'] = ['between',[$start_time,$end_time]];
        }
        if (isset($getInfo['type']) && !empty($getInfo['type'])) {
            if (isset($getInfo['keyword']) && !empty($getInfo['keyword'])) {
                switch ($getInfo['type']) {
                    case 1:
                        $where['url'] = array('like', '%' . trim($getInfo['keyword']) . '%');
                        break;
                    case 2:
                        $where['nickname'] = array('like', '%' . trim($getInfo['keyword']) . '%');
                        break;
                    case 3:
                        $where['uid'] = trim($getInfo['keyword']);
                        break;
                }
            }
        }

        Vendor('phpexcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();

        $list = model('UserAction')->where($where)->select();

        $row = 1;
        //这里可能会报错
        $objPHPExcel->getActiveSheet()->mergeCells("A$row:E$row");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$row",'操作日志报表');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".($row+1),'用户ID');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B".($row+1),'用户昵称');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".($row+1),'操作内容');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D".($row+1),'操作url');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E".($row+1),'操作时间');

        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(30);//设置行高度
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        foreach ($list as $k=>$action){
            $row = $k + 3;

            $addtime =date('Y-m-d H:i:s',$action['addTime']);
            //把数据循环写入excel中
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$row", $action['uid']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$row", $action['nickname']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$row", $action['actionName']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$row", $action['url']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$row", $addtime);

            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(25);//设置行高度
        }

        //设置默认字体
        $objPHPExcel->getDefaultStyle()->getFont()->setName( 'Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

        //设置列宽
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        //设置居中
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $fileName = 'log';
        //导出execl
        header('Content-type: text/html; charset=utf-8');
        header("Content-type:application/vnd.ms-excel");
        header('pragma:public');
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=".$fileName."_".date('Y-m-d').".xls");
        header('Expires:0');
        header('Pragma:public');
        $xls = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $xls->save('php://output');

        echo 'ok';
        exit();
    }

	/**
	 * 
	 * 导出
	 * 
	 */
	public function outputExcel() {
		if ($this->request->isGet()) {
			
			error_reporting(E_ALL);
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);	 
			       
			$product_name = input('get.product_name', '', 'trim');
			$shop_id = input('get.shop_id', '', 'trim');	

			$where = [];
			if ($product_name != '') {
				$where['product_name'] = ['like', '%'.$product_name.'%'];
			}
			
			if ($shop_id != '') {
				$where['shop_id'] = ['like', '%'.$shop_id.'%'];
			}
			
	        $listInfo = model('Product')->where($where)->order('product_id desc')->select();	

	        Vendor('phpexcel.PHPExcel');
        	Vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        	Vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        	Vendor('PHPExcel.PHPExcel.Writer.Excel5');
       		$objExcel = new \PHPExcel();
			$objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
       		$objActSheet = $objExcel->getActiveSheet();
       		$key = ord("A");
       		$objExcel->getProperties()->setCreator('constantine')
                ->setLastModifiedBy('constantine')
                ->setTitle('线路管理_'.date("Y-m-d"))
                ->setSubject('线路管理_'.date("Y-m-d"))
                ->setDescription('constantine for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Result file');
                
       		$letter =explode(',',"A,B,C,D,E,F,G");
        	$arrHeader = array('序号','线路名称','线路单价（元）','销量','上架状态','线路开放时间','所属分类');
       		
        	$objExcel->getActiveSheet()->mergeCells("A1:G1");
	        $objExcel->setActiveSheetIndex(0)->setCellValue("A1",'线路管理_'.date("Y-m-d"));
        	
	        //填充表头信息
	        $lenth =  count($arrHeader);
	        for($i = 0;$i < $lenth;$i++) {
	            $objActSheet->setCellValue("$letter[$i]2","$arrHeader[$i]");
	        };
   		
		    //填充表格信息
		    if ($listInfo) {
		        foreach($listInfo as $k=>$v){
		            $k = $k + 3;
		            
	            	$v['marketable'] = P::$_tv_product['marketable'][$v['marketable']];
	            	$v['product_date'] = $v['beg_date'].' - '.$v['end_date'];
	            	$cate = model('Product')->getCateName($v['cate']); 		            
		            
		            $objActSheet->setCellValue('A'.$k,$v['product_id']);
		            $objActSheet->setCellValue('B'.$k, $v['product_name']);
		            $objActSheet->setCellValue('C'.$k, $v['price_adult']);
		            $objActSheet->setCellValue('D'.$k, $v['total_buy']);
		            $objActSheet->setCellValue('E'.$k, $v['marketable']);
		            $objActSheet->setCellValue('F'.$k, $v['product_date']);
		            $objActSheet->setCellValue('G'.$k, $cate);
		            // 表格高度
		            $objActSheet->getRowDimension($k)->setRowHeight(30);
			        //设置居中
			        $objExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			        $objExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);		            
		        }   
		    }    		
       		
	        //设置表格的宽度
	        $objActSheet->getColumnDimension('A')->setWidth("10");
	        $objActSheet->getColumnDimension('B')->setWidth("80");
	        $objActSheet->getColumnDimension('C')->setWidth("20");
	        $objActSheet->getColumnDimension('D')->setWidth("20");
	        $objActSheet->getColumnDimension('E')->setWidth("20");
	        $objActSheet->getColumnDimension('F')->setWidth("30");
	        $objActSheet->getColumnDimension('G')->setWidth("50");  		
       		
	        $outfile = "线路列表_".date('Y-m-d').'.xlsx';
	        header("Content-Type: application/force-download");
	        header("Content-Type: application/octet-stream");
	        header("Content-Type: application/download");
	        header('Content-Disposition:inline;filename="'.$outfile.'"');
	        header("Content-Transfer-Encoding: binary");
	        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	        header("Pragma: no-cache");
	        $objWriter->save('php://output');       		
	
	        echo 'ok';
	        exit();	        		
		}
		
	}    
}