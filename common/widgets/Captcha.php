<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27 0027
 * Time: 14:19
 */

namespace common\widgets;


class Captcha extends \yii\captcha\Captcha
{
    public function registerClientScript()
    {
        return;
    }
}