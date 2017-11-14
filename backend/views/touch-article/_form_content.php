<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 9:15
 */
use yii\widgets\ActiveForm;

?>

<?= $form->field($model,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        'initialFrameHeight' => '600',
        'autoHeightEnabled' => true,
        'topOffset' => 50,
    ],
]); ?>

