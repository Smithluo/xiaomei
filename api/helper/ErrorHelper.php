<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29 0029
 * Time: 21:40
 */

namespace api\helper;


class ErrorHelper
{
    /**
     * 获取第一个错误提示
     * @param $model
     * @return mixed
     */
    public static function getFirstError($model) {
        foreach ($model->errors as $property => $errorList) {
            return $model->getFirstError($property);
        }
    }
}