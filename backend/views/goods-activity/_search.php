<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\GoodsActivity;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-activity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'act_type')->dropDownList(GoodsActivity::$act_type_map) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'act_name') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'goods_name') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'act_id') ?>
        </div>
        <div class="form-group col-lg-1">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>




    <?php ActiveForm::end(); ?>

</div>
