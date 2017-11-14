<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/13 0013
 * Time: 16:34
 */

namespace common\widgets;


class ActiveField extends \yii\widgets\ActiveField
{

    public function begin()
    {
        if ($this->form->enableClientScript) {
            $clientOptions = $this->getClientOptions();
            if (!empty($clientOptions)) {
                $this->form->attributes[] = $clientOptions;
            }
        }
        return '';
    }

    public function end()
    {
        return '';
    }
}