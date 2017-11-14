<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/17
 * Time: 12:12
 */

namespace common\helper;

use \Yii;

class TextHelper
{
    /**
     * 输出富文本
     * @param $str
     * @return string
     */
    public static function html_out($str) {
        if (function_exists('htmlspecialchars_decode')) {
            $str = htmlspecialchars_decode($str);
        } else {
            $str = html_entity_decode($str);
        }
        $str = stripslashes($str);
        return $str;
    }

    /**
     * 输出富文本
     * @param $content
     * @return mixed
     */
    public static function formatRichText($content)
    {
        $content = htmlspecialchars_decode($content);
        $content = stripslashes($content);
        $shop_config = CacheHelper::getShopConfigParams('IMG_BASE_URL');
        if (!empty($shop_config['IMG_BASE_URL']['value'])) {
            $img_base_url = $shop_config['IMG_BASE_URL']['value'];
        } else {
            $img_base_url = Yii::$app->params['shop_config']['img_base_url'];
        }

        return str_replace("/data/attached", $img_base_url, $content);
    }

    /**
     * 富文本在后台编辑时显示图片站域名下的对应url，方便排板
     * @param $content
     * @return mixed
     */
    public static function replaceImagePath($content)
    {
        $shop_config = CacheHelper::getShopConfigParams('IMG_BASE_URL');
        if (!empty($shop_config['IMG_BASE_URL']['value'])) {
            $img_base_url = $shop_config['IMG_BASE_URL']['value'];
        } else {
            $img_base_url = Yii::$app->params['shop_config']['img_base_url'];
        }

        return str_replace($img_base_url, "/data/attached", $content);
    }

    /**
     * 替换字符串数组的 分隔符
     * @param $str
     * @param string $search
     * @param string $replace
     * @return string
     */
    public static function replaceDelimter($str, $search = '，', $replace = ',')
    {
        $replaced_str = str_replace($search, $replace, $str);

        $replaced_array = explode($replace, $replaced_str);
        //  除去空格
        $trimed_array = array_map('trim', $replaced_array);
        //  除去重复值
        $uniqe_array = array_unique($trimed_array);
        //  除去空值
        $not_null_array = array_filter($uniqe_array);
        $array = implode($replace, $not_null_array);

        return $array;
    }

    /**
     * 截取UTF-8编码下字符串的函数
     *
     * @param   string      $str        被截取的字符串
     * @param   int         $length     截取的长度
     * @param   bool        $append     是否附加省略号
     *
     * @return  string
     */
    public static function sub_str($str, $length = 0, $append = true) {
        $str = trim($str);
        $strlength = strlen($str);

        if ($length == 0 || $length >= $strlength) {
            return $str;
        } elseif ($length < 0) {
            $length = $strlength + $length;
            if ($length < 0) {
                $length = $strlength;
            }
        }

        if (function_exists('mb_substr')) {
            $newstr = mb_substr($str, 0, $length, 'utf-8');
        } elseif (function_exists('iconv_substr')) {
            $newstr = iconv_substr($str, 0, $length, 'utf-8');
        } else {
            //$newstr = trim_right(substr($str, 0, $length));
            $newstr = substr($str, 0, $length);
        }

        if ($append && $str != $newstr) {
            $newstr .= '...';
        }

        return $newstr;
    }

    public static function isMobile($mobile) {
        if(preg_match("/1[1234567890]{1}\d{9}$/",$mobile)){
            return true;
        }
        return false;
    }

    /**
     * 获取模型入库时的错误信息
     * @param $errors
     * @return string
     */
    public static function getErrorsMsg($errors) {
        $msg = '';
        foreach ($errors as $value) {
            foreach ($value as $item) {
                $msg .= $item.',';
            }
        }

        return $msg;
    }

    /**
     * 处理序列化的支付、配送的配置参数
     * 返回一个以name为索引的数组
     *
     * @param $cfg
     * @return array|bool
     */
    public static function unserializeConfig($cfg) {
        if (is_string($cfg) && ($arr = unserialize($cfg)) !== false) {
            $config = array();

            foreach ($arr AS $key => $val) {
                $config[$val['name']] = $val['value'];
            }

            return $config;
        } else {
            return false;
        }
    }

    /**
     * 创建像这样的查询: "IN('a','b')";
     *
     * @access   public
     * @param    mix      $item_list      列表数组或字符串
     * @param    string   $field_name     字段名称
     *
     * @return   void
     */
    public static function dbCreateIn($item_list, $field_name = '') {
        if (empty($item_list)) {
            return $field_name . " IN ('') ";
        } else {
            if (!is_array($item_list)) {
                $item_list = explode(',', $item_list);
            }
            $item_list = array_unique($item_list);
            $item_list_tmp = '';
            foreach ($item_list AS $item) {
                if ($item !== '') {
                    $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
                }
            }
            if (empty($item_list_tmp)) {
                return $field_name . " IN ('') ";
            } else {
                return $field_name . ' IN (' . $item_list_tmp . ') ';
            }
        }
    }

    /**
     * 数据过滤函数
     * @param string|array $data 待过滤的字符串或字符串数组
     * @param boolean $force 为true时忽略get_magic_quotes_gpc
     * @return array|string
     */
    public static function hackFilter($data, $force = false) {
        if (is_string($data)) {
            $data = trim(htmlspecialchars($data)); // 防止被挂马，跨站攻击
            if (($force == true) || (!get_magic_quotes_gpc())) {
                $data = addslashes($data); // 防止sql注入
            }
            return $data;
        } else if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = in($value, $force);
            }
            return $data;
        } else {
            return $data;
        }
    }
}