<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/15 0015
 * Time: 16:46
 */

namespace common\widgets;


use yii\widgets\LinkPager;

class RightLinkPager extends LinkPager
{
    public $options = ['class' => 'pagination pull-right'];

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub
    }
}