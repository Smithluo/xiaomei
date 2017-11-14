<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 5:11 PM
 */

namespace api\helper;


class ResultHelper
{
    /**
     * 返回json串
     * @param $result
     * @return mixed|string
     */
    public static function jsonFormat($result)
    {
        return json_encode($result);
    }

}