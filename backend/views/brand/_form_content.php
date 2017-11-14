<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10 0010
 * Time: 21:57
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Brand;
use common\helper\DateTimeHelper;

?>

<div>
    <?php
    if (isset($touchBrand)) {
        echo $form->field($touchBrand,'brand_content')->widget('kucha\ueditor\UEditor',[]);
    }
    else {
        echo $form->field($model->touchBrand,'brand_content')->widget('kucha\ueditor\UEditor',[]);
    }
    ?>
    <?= $form->field($model, 'turn_show_time')->hiddenInput([
        'value' => DateTimeHelper::getFormatGMTDateTime()
    ])?>
</div>
