<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/7
 * Time: 16:10
 */

namespace common\helper;

use common\models\ShopConfig;
use yii\caching\Cache;

class DateTimeHelper
{
    /**
     * 获取时间值距离现在的时间
     */
    public static function getLastTime($timestamp)
    {
        $time_zone = ShopConfig::getConfigValue('timezone');
        $time_diff = abs(time() - $timestamp - $time_zone * 3600);
        if ($time_diff >= 86400) {
            return floor($time_diff / 86400).' 天';
        } elseif ($time_diff >= 3600) {
            return  floor($time_diff / 3600).' 小时';
        } elseif ($time_diff >= 60) {
            return  floor($time_diff / 60).' 分钟';
        } elseif ($time_diff > 0) {
            return  $time_diff.' 秒';
        }
    }

    /**
     * 计算剩余的时段
     * @param $time_diff
     * @return string
     */
    public static function getTimePeriod($time_diff)
    {
        $timePeriod = '';
        if ($time_diff >= 86400) {
            $day = floor($time_diff / 86400);
            if (!empty($day)) {
                $timePeriod .= $day.'天';
            }
            $time_diff = $time_diff % 86400;
        }

        if ($time_diff >= 3600) {
            $hour = floor($time_diff / 3600);
            if (!empty($hour)) {
                $timePeriod .= $hour.'小时';
            }
            $time_diff = $time_diff % 3600;
        }

        if ($time_diff >= 60) {
            $min = floor($time_diff / 60);
            if (!empty($min)) {
                $timePeriod .= $min.'分';
            }
            $time_diff = $time_diff % 60;
        }

        if ($time_diff > 0) {
            $sec = $time_diff;
            if (!empty($sec)) {
                $timePeriod .= $sec.'秒';
            }
        }

        return $timePeriod;
    }

    /**
     * 获取时间值距离现在的时间
     */
    public static function getLastTimeMap($cnTimestamp)
    {
        $lastTime = [
//            'day' => 0,
            'hour' => '00',
            'min' => '00',
            'min' => '00'
        ];
        $time_diff = abs(time() - $cnTimestamp);
//        if ($time_diff >= 86400) {
//            $day = floor($time_diff / 86400);
//            $lastTime['day'] = str_pad($day, 2, 0, STR_PAD_LEFT);
//            $time_diff = $time_diff % 86400;
//        }

        if ($time_diff >= 3600) {
            $hour = floor($time_diff / 3600);
            $lastTime['hour'] = str_pad($hour, 2, 0, STR_PAD_LEFT);
            $time_diff = $time_diff % 3600;
        }

        if ($time_diff >= 60) {
            $min = floor($time_diff / 60);
            $lastTime['min'] = str_pad($min, 2, 0, STR_PAD_LEFT);
            $time_diff = $time_diff % 60;
        }

        if ($time_diff > 0) {
            $sec = $time_diff;
            $lastTime['sec'] = str_pad($sec, 2, 0, STR_PAD_LEFT);
        }

        return $lastTime;
    }

    /**
     * 获取指定时间戳的yyyy-mm-dd HH:ii:ss
     * @param $timestamp
     * @return bool|string
     */
    public static function getFormatDateTime($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * 获取指定时间出戳的年月日
     * @param $timestamp
     * @return bool|string
     */
    public static function getFormatDate($timestamp, $format = 'Y-m-d')
    {
        if (!$timestamp) {
            $timestamp = time();
        }
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        return date($format, $timestamp);
    }

    /**
     * 获取当前日期，主要用于建立文件目录，如20160617
     * @return bool|string
     */
    public static function getFormatDateNow()
    {
        return date('Ymd');
    }

    public static function getFormatDateTimeNow() {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取当前时间戳微秒数的整数值
     * 主要用于生成文件名
     * @return string
     */
    public static function getFormatMtime()
    {
        $time = microtime();
        $time_array = explode(' ', $time);

        return ''.$time_array[1].$time_array[0]*1000000;
    }

    /**
     * 获取日期的开始时间（格林威治时间当天0点）
     * @param $str
     * @return int
     */
    public static function getDateBegin($str)
    {
        if (!is_numeric($str)) {
            $str = strtotime($str);
        }

        $date = date('Y-m-d 00:00:00', $str);
        return strtotime($date);
    }

    /**
     * 获取日期的结束时间（格林威治时间当天24点）
     * @param $str
     * @return int
     */
    public static function getDateEnd($str)
    {
        if (!is_numeric($str)) {
            $str = strtotime($str);
        }

        $date = date('Y-m-d 23:59:59', $str);
        return strtotime($date);
    }

    /**
     * 获取日期的开始时间（北京时间当天0点）
     * @param $str
     * @param $type datetime | timestamp
     * @return int
     */
    public static function getGMTDateBegin($str, $type = 'datetime')
    {
        $timezone = ShopConfig::getConfigValue('timezone');
        if (!is_numeric($str)) {
            $str = strtotime($str);
        }

        if ($type == 'timestamp') {
            return $str + $timezone * 3600;
        } else {
            return self::getFormatDateTime($str + $timezone * 3600);
        }
    }

    /**
     * 获取日期的结束时间（北京时间当天24点）
     * @param $str
     * @return int
     */
    public static function getGMTDateEnd($str, $type = 'datetime')
    {
        $shop_config = CacheHelper::getShopConfigParams('timezone');
        if (isset($shop_config['timezone']['value'])) {
            $timezone = 24 + $shop_config['timezone']['value'];
        } else {
            $timezone = 24 + ShopConfig::getConfigValue('timezone');
        }
        if (!is_numeric($str)) {
            $str = strtotime($str);
        }

        if ($type == 'timestamp') {
            return $str + $timezone * 3600 - 1;
        } else {
            return self::getFormatDateTime($str + $timezone * 3600 - 1);
        }
    }

    /**
     * 转换成中国时区的日期
     * @param $time
     * @return bool|string
     */
    public static function getFormatCNDate($time = 0, $format = 'Y-m-d')
    {
        if (!$time) {
            $time = time();
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $timezone = ShopConfig::getConfigValue('timezone');

        return date($format, $time + $timezone * 3600);
    }

    /**
     * 转换成中国时区的日期时间
     * @param $time
     * @return bool|string
     */
    public static function getFormatCNDateTime($time, $format = 'Y-m-d H:i:s')
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
//        $timezone = ShopConfig::getConfigValue('timezone');
        $timezone = CacheHelper::getShopConfigParams('timezone')['value'];

        return date($format, $time + $timezone * 3600);
    }

    /**
     * 转换成格林威治的日期时间
     * 主要用于查询时段，数据库中是格林威治时间，用户看到的是北京时间
     * @param $time
     * @return bool|string
     */
    public static function getFormatGMTDateTime($time = '')
    {
        if (!$time) {
            $time = time();
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $timezone = ShopConfig::getConfigValue('timezone');

        return date('Y-m-d H:i:s', $time - $timezone * 3600);
    }

    /**
     * 转换成格林威治的日期时间
     * 主要用于入库
     * @param $time
     * @return bool|string
     */
    public static function getFormatGMTTimesTimestamp($time = '')
    {
        if (empty($time)) {
            $time = time();
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $timezone = ShopConfig::getConfigValue('timezone');

        return $time - $timezone * 3600;
    }

    /**
     * 转换成格林威治的日期时间
     * 主要用于入库
     * @param $time
     * @return bool|string
     */
    public static function getFormatCNTimesTimestamp($time = '')
    {
        if (empty($time)) {
            $time = time();
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $timezone = ShopConfig::getConfigValue('timezone');

        return $time + $timezone * 3600;
    }

    /**
     * 获得当前格林威治时间的时间戳
     *
     * @return  integer
     */
    public static function gmtime()
    {
        return (time() - date('Z'));
    }

    /**
     * 获取当前的 时间戳+微秒数
     * @return float
     */
    public static function getMicroTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$sec + (float)$usec);
    }

    /**
     * 把时间(秒数) 转换成时间文本
     * @param int $seconds
     * @return string
     */
    public static function getTimeDesc($seconds)
    {
        $restSeconds = (int)$seconds;
        $min = 60;
        $hour = 3600;
        $day = 86400;

        $timeDesc = '';
        if ($restSeconds > $day) {
            $dayCount = floor($restSeconds / $day);
            $restSeconds = $restSeconds % $day;

            $timeDesc .= $dayCount.'天';
        }

        if ($restSeconds > $day) {
            $hourCount = floor($restSeconds / $hour);
            $restSeconds = $restSeconds % $hour;

            $timeDesc .= $hourCount.'时';
        }

        if ($restSeconds > $min) {
            $minCount = floor($restSeconds / $min);
            $restSeconds = $restSeconds % $min;

            $timeDesc .= $minCount.'分';
        }

        if ($restSeconds) {
            $timeDesc .= $restSeconds.'秒';
        }

        return $timeDesc;
    }
}