<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;
use backend\models\EventRule;

/* @var $this yii\web\View */
/* @var $model common\models\EventRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-rule-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-4\">{input}</div>\n
                <div class=\"col-lg-3\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label text-right'],
        ],
    ]); ?>
<div class="col-lg-9">
    <?= $form->field($model, 'rule_name')->textInput(['maxlength' => 80]) ?>

    <?= $form->field($model, 'match_type')->dropDownList(EventRule::$match_type_map) ?>

    <?= $form->field($model, 'match_value')->textInput() ?>

    <?= $form->field($model, 'match_effect')->dropDownList(EventRule::$match_effect_map) ?>

    <?= $form->field($model, 'gift_id')->textInput() ?>

    <?= $form->field($model, 'gift_num')->textInput() ?>

    <?= $form->field($model, 'gift_show_peice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gift_need_pay')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $giftEventMap,
            'options' => ['placeholder' => '请选择活动'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
</div>
<div class="col-lg-3">
    <p>
        时间充足的时候会在这里补充商品搜索功能 主要显示 商品id、名称、品牌
    </p>
    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => DateTimeHelper::getFormatGMTTimesTimestamp(time())])->label('') ?>
</div>
    <div class="form-group col-lg-12">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
