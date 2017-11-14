<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 14:55
 */

namespace common\helper;

use Yii;
use common\models\ShopConfig;

class ImageHelper
{
    /**
     * 图片路径转换
     * @param string $image
     * @param bool $thumb
     * @param string $call
     * @param bool $del
     * @return string
     */
    public static function get_image_path($image='', $thumb=false, $call='goods', $del=false)
    {
        $shop_config = CacheHelper::getShopConfigParams(['no_picture', 'IMG_BASE_URL', 'shop_url']);
        $url = $shop_config['no_picture']['value'];
        if (isset($shop_config['IMG_BASE_URL']['value'])) {
            $cfg_img_base_url = $shop_config['IMG_BASE_URL']['value'];
        } else {
            $cfg_img_base_url = Yii::$app->params['shop_config']['img_base_url'];
        }

        $img_base_url = trim($cfg_img_base_url, '/');
        $cfg_shop_url = $shop_config['shop_url']['value'];
        if(!empty($image)){
            if (strtolower(substr($image, 0, 4)) == 'http'){
                $url = $image;
            } elseif (strtolower(substr($image, 0, 13)) == 'data/attached'){
                $image = substr($image, 13);
                $url = $img_base_url . $image;
            } elseif (strtolower(substr($image, 0, 14)) == '/data/attached'){
                $image = substr($image, 14);
                $url = $img_base_url . $image;
            } else {
                $base_url = substr($cfg_shop_url, -1) == '/'
                    ? $cfg_shop_url
                    : $cfg_shop_url . '/';
                $url = $base_url . $image;
            }
        }
        return (string)$url;
    }

    /**
     * 新后台中添加了团拼活动，改用新的方式拼接图片路径
     */
    public static function getNewGoodsActivityImg($img)
    {
        if (strpos($img, 'attached')) {
            return self::get_image_path($img);
        } else {
            $img_base_url = CacheHelper::getShopConfigParams('IMG_BASE_URL');
            return $img_base_url['value'].'/goods_activity/'.$img;
        }
    }

    public static function unique_name($dir) {
        $filename = '';
        while (empty($filename)) {
            $filename = self::random_filename();
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png')) {
                $filename = '';
            }
        }

        return $filename;
    }

    /**
     * 创建图片的缩略图
     *
     * @access  public
     * @param   string      $img    原始图片的路径
     * @param   int         $thumb_width  缩略图宽度
     * @param   int         $thumb_height 缩略图高度
     * @param   strint      $path         指定生成图片的目录名
     * @return  mix         如果成功返回缩略图的路径，失败则返回false
     */
    public static function make_thumb($img, $thumb_width = 0, $thumb_height = 0, $path = '', $bgcolor = '') {
        /* 检查缩略图宽度和高度是否合法 */
        if ($thumb_width == 0 && $thumb_height == 0) {
            return str_replace(ROOT_PATH, '', str_replace('\\', '/', realpath($img)));
        }

        /* 检查原始文件是否存在及获得原始文件的信息 */  //  获取原始图片的后缀名
        $org_info = @getimagesize($img);
        if (!$org_info) {
            return [
                'code' => 1,
                'msg' => '找不到原始图片',
            ];

            return false;
        }


        $img_org = imagecreatefromgif($img, $org_info[2]);

        /* 原始图片以及缩略图的尺寸比例 */
        $scale_org = $org_info[0] / $org_info[1];
        /* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
        if ($thumb_width == 0) {
            $thumb_width = $thumb_height * $scale_org;
        }
        if ($thumb_height == 0) {
            $thumb_height = $thumb_width / $scale_org;
        }

        /* 创建缩略图的标志符,默认开启了GD2 */
        $img_thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        /* 背景颜色 */
        if (empty($bgcolor)) {
            $bgcolor = '#FFFFFF';
        }
        $bgcolor = trim($bgcolor, "#");
        sscanf($bgcolor, "%2x%2x%2x", $red, $green, $blue);
        $clr = imagecolorallocate($img_thumb, $red, $green, $blue);
        imagefilledrectangle($img_thumb, 0, 0, $thumb_width, $thumb_height, $clr);

        if ($org_info[0] / $thumb_width > $org_info[1] / $thumb_height) {
            $lessen_width = $thumb_width;
            $lessen_height = $thumb_width / $scale_org;
        } else {
            /* 原始图片比较高，则以高度为准 */
            $lessen_width = $thumb_height * $scale_org;
            $lessen_height = $thumb_height;
        }

        $dst_x = ($thumb_width - $lessen_width) / 2;
        $dst_y = ($thumb_height - $lessen_height) / 2;

        /* 将原始图片进行缩放处理,默认支持GD2 */
        imagecopyresampled($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info[0], $org_info[1]);

        /* 创建当月目录 */
        if (empty($path)) {
            $dir = ROOT_PATH . '/data/attached/images/' . DateTimeHelper::getFormatDateNow() . '/';
        } else {
            $dir = $path;
        }


        /* 如果目标目录不存在，则创建它 */
        if (!file_exists($dir)) {
            if (!make_dir($dir)) {
                /* 创建目录失败 */
                return [
                    'code' => 4,
                    'msg' => '指定的目录没没有写权限'
                ];
            }
        }

        /* 如果文件名为空，生成不重名随机文件名 */
        $filename = self::unique_name($dir, $ext);

        /* 生成文件 */
        if (function_exists('imagejpeg')) {
            $filename .= '.jpg';
            imagejpeg($img_thumb, $dir . $filename);
        } elseif (function_exists('imagegif')) {
            $filename .= '.gif';
            imagegif($img_thumb, $dir . $filename);
        } elseif (function_exists('imagepng')) {
            $filename .= '.png';
            imagepng($img_thumb, $dir . $filename);
        } else {
            return [
                'code' => 2,
                'msg' => '缩略图生成失败',
            ];
        }

        imagedestroy($img_thumb);
        imagedestroy($img_org);

        //确认文件是否生成
        if (file_exists($dir . $filename)) {
            return str_replace(ROOT_PATH, '', $dir) . $filename;
        } else {
            return [
                'code' => 4,
                'msg' => '指定的目录没没有写权限'
            ];;
        }
    }

    /**
     * 为图片增加水印
     *
     * @access      public
     * @param       string      filename            原始图片文件名，包含完整路径
     * @param       string      target_file         需要加水印的图片文件名，包含完整路径。如果为空则覆盖源文件
     * @param       string      $watermark          水印完整路径
     * @param       int         $watermark_place    水印位置代码
     * @return      mix         如果成功则返回文件路径，否则返回false
     */
    function add_watermark($filename, $target_file = '', $watermark = '', $watermark_place = '', $watermark_alpha = 0.65) {
        // 是否安装了GD
        $gd = $this->gd_version();
        if ($gd == 0) {
            $this->error_msg = $GLOBALS['_LANG']['missing_gd'];
            $this->error_no = ERR_NO_GD;

            return false;
        }

        // 文件是否存在
        if ((!file_exists($filename)) || (!is_file($filename))) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['missing_orgin_image'], $filename);
            $this->error_no = ERR_IMAGE_NOT_EXISTS;

            return false;
        }

        /* 如果水印的位置为0，则返回原图 */
        if ($watermark_place == 0 || empty($watermark)) {
            return str_replace(ROOT_PATH, '', str_replace('\\', '/', realpath($filename)));
        }

        if (!$this->validate_image($watermark)) {
            /* 已经记录了错误信息 */
            return false;
        }

        // 获得水印文件以及源文件的信息
        $watermark_info = @getimagesize($watermark);
        $watermark_handle = $this->img_resource($watermark, $watermark_info[2]);

        if (!$watermark_handle) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['create_watermark_res'], $this->type_maping[$watermark_info[2]]);
            $this->error_no = ERR_INVALID_IMAGE;

            return false;
        }

        // 根据文件类型获得原始图片的操作句柄
        $source_info = @getimagesize($filename);
        $source_handle = $this->img_resource($filename, $source_info[2]);
        if (!$source_handle) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['create_origin_image_res'], $this->type_maping[$source_info[2]]);
            $this->error_no = ERR_INVALID_IMAGE;

            return false;
        }

        // 根据系统设置获得水印的位置
        switch ($watermark_place) {
            case '1':
                $x = 0;
                $y = 0;
                break;
            case '2':
                $x = $source_info[0] - $watermark_info[0];
                $y = 0;
                break;
            case '4':
                $x = 0;
                $y = $source_info[1] - $watermark_info[1];
                break;
            case '5':
                $x = $source_info[0] - $watermark_info[0];
                $y = $source_info[1] - $watermark_info[1];
                break;
            default:
                $x = $source_info[0] / 2 - $watermark_info[0] / 2;
                $y = $source_info[1] / 2 - $watermark_info[1] / 2;
        }

        if (strpos(strtolower($watermark_info['mime']), 'png') !== false) {
            imageAlphaBlending($watermark_handle, true);
            imagecopy($source_handle, $watermark_handle, $x, $y, 0, 0, $watermark_info[0], $watermark_info[1]);
        } else {
            imagecopymerge($source_handle, $watermark_handle, $x, $y, 0, 0, $watermark_info[0], $watermark_info[1], $watermark_alpha);
        }
        $target = empty($target_file) ? $filename : $target_file;

        switch ($source_info[2]) {
            case 'image/gif':
            case 1:
                imagegif($source_handle, $target);
                break;

            case 'image/pjpeg':
            case 'image/jpeg':
            case 2:
                imagejpeg($source_handle, $target);
                break;

            case 'image/x-png':
            case 'image/png':
            case 3:
                imagepng($source_handle, $target);
                break;

            default:
                $this->error_msg = $GLOBALS['_LANG']['creating_failure'];
                $this->error_no = ERR_NO_GD;

                return false;
        }

        imagedestroy($source_handle);

        $path = realpath($target);
        if ($path) {
            return str_replace(ROOT_PATH, '', str_replace('\\', '/', $path));
        } else {
            $this->error_msg = $GLOBALS['_LANG']['writting_failure'];
            $this->error_no = ERR_DIRECTORY_READONLY;

            return false;
        }
    }

    /**
     *  检查水印图片是否合法
     *
     * @access  public
     * @param   string      $path       图片路径
     *
     * @return boolen
     */
    function validate_image($path) {
        if (empty($path)) {
            $this->error_msg = $GLOBALS['_LANG']['empty_watermark'];
            $this->error_no = ERR_INVALID_PARAM;

            return false;
        }

        /* 文件是否存在 */
        if (!file_exists($path)) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['missing_watermark'], $path);
            $this->error_no = ERR_IMAGE_NOT_EXISTS;
            return false;
        }

        // 获得文件以及源文件的信息
        $image_info = @getimagesize($path);

        if (!$image_info) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['invalid_image_type'], $path);
            $this->error_no = ERR_INVALID_IMAGE;
            return false;
        }

        /* 检查处理函数是否存在 */
        if (!$this->check_img_function($image_info[2])) {
            $this->error_msg = sprintf($GLOBALS['_LANG']['nonsupport_type'], $this->type_maping[$image_info[2]]);
            $this->error_no = ERR_NO_GD;
            return false;
        }

        return true;
    }


    /**
     * 新后台中添加了团拼活动，改用新的方式拼接图片路径
     * @param $img
     * @return string
     */
    public static function getGoodsActivityImgPath($img)
    {
        if (strpos($img, 'attached')) {
            return self::get_image_path($img);
        } else {
            return Yii::$app->params['shop_config']['img_base_url'].'/goods_activity/'.$img;
        }
    }

    //上传图片功能
    public static function upload_upgrade_img($file, $path , $pic_name)
    {
        $file_type = strtolower($file['type']);
        //判断文件类型
        $allow_img_ext = ['jpg', 'jpeg', 'png'];
        $allow_img_type = [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/pjpeg',
            'image/x-png'  ];
        if(!in_array($file_type, $allow_img_type)) {
            $text=implode(",", $allow_img_ext);
            return  [
                'code' => 1,
                'msg' => '您上传的图片类型不正确,可支持的类型 '.$text,
            ];
        } else {
            if (!is_uploaded_file($file['tmp_name'])) {
                return  [
                    'code' => 2,
                    'msg' => '上传失败',
                ];
            } else {
                //  定义图片名称，避免重复
                if ($file['error'] > 0) {
                    return  [
                        'code' => 2,
                        'msg' => '上传失败',
                    ];
                } elseif ($file['size10'] > 5242880) {
                    return   [
                        'code' => 3,
                        'msg' => '上传的图片大小太大,最大限制为5M',
                    ];
                }

                $pinfo = explode('.', $file['name']);
                $file_ext = end($pinfo);

                //判断文件加是否存在
                if(!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                if (!move_uploaded_file($file['tmp_name'], $path .  $_SESSION['user_id'] .'_'. $pic_name.'.' . $file_ext)) {
                    return  [
                        'code' => 4,
                        'msg' => '没有对应的文件夹',
                    ];
                } else {
                    return [
                        'code' => 0,
                        'msg' => '上传成功,对应的文件名是'.$_SESSION['user_id'] . $pic_name.'.' . $file_ext,
                        'fileName' => $path.$_SESSION['user_id'].'_'. $pic_name.'.' . $file_ext ,
                    ];
                }
            }
        }
    }

}