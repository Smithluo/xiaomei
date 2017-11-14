<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CashRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ibox-content m-b-sm border-bottom">

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'enableClientScript' => false,
            'fieldClass' => 'common\widgets\ActiveField',
        ]); ?>

        <?= $form->field($model, 'date_added', [
            'template'=>'
            <div class="col-sm-3">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {input}
                    </div>
                </div>
            </div>',
            'labelOptions'=>['class'=>'control-label', 'for'=>'date_added'],
            'inputOptions'=>['id'=>'date_added', 'class'=>'form-control'],
        ]) ?>

        <?= $form->field($model, 'date_modified', [
            'template'=>'
            <div class="col-sm-3">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {input}
                    </div>
                </div>
            </div>',
            'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
            'inputOptions'=>['id'=>'date_modified', 'class'=>'form-control'],
        ]) ?>

        <?= $form->field($model, 'search_type', [
            'template'=>'{label}{input}',
            'labelOptions'=>['style' => 'display: none;'],
        ])->radioList(['0'=>'全部流水', '1'=>'收入记录', '2'=>'支出记录'], [
            'tag' => false,
            'item' => function($index, $label, $name, $checked, $value) {
                if($checked) {
                    $res = '<div class="radio radio-success radio-inline" style="margin-top: 25px;">
                        <input type="radio" id="inlineRadio'.$index.'" value="'.$value.'" name="'.$name.'" checked="'.$checked.'">
                        <label for="inlineRadio'.$index.'"> '.$label.' </label>
                    </div>';
                }
                else {
                    $res = '<div class="radio radio-success radio-inline" style="margin-top: 25px;">
                        <input type="radio" id="inlineRadio'.$index.'" value="'.$value.'" name="'.$name.'">
                        <label for="inlineRadio'.$index.'"> '.$label.' </label>
                    </div>';
                }

                return $res;
            }
        ]) ?>

        <?= Html::submitButton('筛选', ['class' => 'btn btn-w-m btn-pink', 'style' => 'margin-top: 22px;']) ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>

