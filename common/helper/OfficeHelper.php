<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 18:03
 */

namespace common\helper;

use \Yii;

//require_once dirname(dirname((dirname(__FILE__)))).'/vendor/PHPExcel/Classes/PHPExcel.php';
//require_once dirname(dirname((dirname(__FILE__)))).'/vendor/PHPExcel/Classes/PHPExcel/Settings.php';
//require_once dirname(dirname((dirname(__FILE__)))).'/vendor/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
//require_once dirname(dirname((dirname(__FILE__)))).'/vendor/PHPExcel/Classes/PHPExcel/CachedObjectStorageFactory.php';

class OfficeHelper
{
    /**
     * 导出Excel表格
     *
     * @param string $file_name 文件名
     * @param string $data_array    数据
     * @param string $style_array   样式
     */
    public static function excelExport($file_name = '', $data_array = '', $style_array = '', $sheetTitle = '', $defaultFormat = true)
    {
        Yii::trace(__FUNCTION__.'导出用户列表——调用导出开始');
        $objectPHPExcel = new \PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        Yii::trace(__FUNCTION__.'导出用户列表——创建 $objectPHPExcel 对象成功');
        $row = 1;
        foreach ($data_array as $data) {
            $col = 0;
            foreach ($data as $item) {
                if ($defaultFormat) {
                    $objectPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item);
                }
                else {
                    $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($col);
                    $coordinate = $columnLetter . $row;
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit($coordinate, $item);
                }
                $col++;
            }
            $row++;
        }

        if (!empty($sheetTitle)) {
            $objectPHPExcel->getActiveSheet()->setTitle($sheetTitle);
        }

        ob_end_clean();
        ob_start();
        Yii::trace(__FUNCTION__.'导出用户列表——填充excel表格成功');
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="' .$file_name. '.xlsx"');
        $objWriter = new \PHPExcel_Writer_Excel2007($objectPHPExcel);
        $objWriter->save('php://output');
        Yii::trace(__FUNCTION__.'导出用户列表——输出excel表格成功');
    }
}