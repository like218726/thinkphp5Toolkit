<?php

namespace app\admin\controller;

class Log extends Base {
    /**
     * 
     * 导出
     * 
     */
    public function export(){
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
}