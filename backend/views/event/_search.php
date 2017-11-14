<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>
    <div class="col-lg-3">
        <?= $form->field($model, 'event_type')->dropDownList($eventTypeMap, ['prompt' => '请选择活动类型']) ?>

        <?=$form->field($model, 'event_id')
            ->widget(\kartik\widgets\Select2::classname(), [
                'data' => $eventNameMap,
                'options' => ['placeholder' => '选择活动名称'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'event_name') ?>

        <?= $form->field($model, 'event_desc') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'rule_id') ?>

        <?php  echo $form->field($model, 'is_active')->dropDownList($is_active_map, ['prompt' => '请选择活动状态']) ?>
    </div>
    <div class="col-lg-3">


        <div class="form-group">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
<!--            --><?php //echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php // echo $form->field($model, 'start_time') ?>

<?php // echo $form->field($model, 'end_time') ?>

<?php // echo $form->field($model, 'updated_at') ?>

<?php // echo $form->field($model, 'updated_by') ?>

