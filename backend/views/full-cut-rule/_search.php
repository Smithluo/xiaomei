<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\FullCutRule;

/* @var $this yii\web\View */
/* @var $model backend\models\FullCutRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="full-cut-rule-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="col-lg-3">
        <?= $form->field($model, 'rule_id') ?>

        <?= $form->field($model, 'rule_name') ?>
    </div>

    <div class="col-lg-3">
<!--        --><?//= $form->field($model, 'event_id')->dropDownList($eventList, ['prompt' => '请选择']) ?>
        <?= $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::className(), [
            'data' => $eventList,
            'options' => ['placeholder' => '选择活动'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

        <?= $form->field($model, 'above') ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'cut') ?>

        <?php  echo $form->field($model, 'status')->dropDownList(FullCutRule::$statusMap, ['prompt' => '请选择']) ?>
    </div>

    <div class="col-lg-3">
        <div class="form-group">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
<!--            --><?php //echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
