<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Ad;

/* @var $this yii\web\View */
/* @var $model common\models\Ad */
/* @var $form yii\widgets\ActiveForm */

//  开始时间为30天前 3600 * 24 * 30 = 2592000
$start_date = date('Y-m-d H:i:s', time() - 2592000);
?>

<div class="ad-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'position_id')->widget(kartik\widgets\Select2::className(), [
                'data' => $adPositions,
            ]) ?>
        </div>
        <div class="col-lg-2">
            <?php echo $form->field($model, 'media_type')->dropDownList(['0' => '图片']); ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'ad_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-5">
            <?= $form->field($model, 'ad_link')->textarea(['maxlength' => true]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-2">
            <!-- 活动开始时间 start -->
            <?= $model->attributeLabels()['start_time']; ?>
            <?= \kartik\datetime\DateTimePicker::widget([
                'name' => 'Ad[start_time]',
                'value' => \common\helper\DateTimeHelper::getFormatCNDate($model->start_time),
                'options' => ['placeholder' => \common\helper\DateTimeHelper::getFormatCNDate(time())],
                'convertFormat' => true,
                'pluginOptions' => [
                    'minView' => 2,
                    'format' => 'yyyy-MM-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
//                    'startDate' => $start_date,
                ]
            ]);?>
            <!-- 活动开始时间 end -->
        </div>
        <div class="col-lg-2">
            <label>
                <?= $model->attributeLabels()['end_time']; ?>
            </label>
            <?= \kartik\datetime\DateTimePicker::widget([
                'name' => 'Ad[end_time]',
                'value' => \common\helper\DateTimeHelper::getFormatCNDate($model->end_time),
                'options' => [
                    'placeholder' => \common\helper\DateTimeHelper::getFormatCNDate(time())],
                'convertFormat' => true,
                'pluginOptions' => [
                    'minView' => 2,
                    'format' => 'yyyy-MM-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'startDate' => $start_date,
                ]
            ]);?>
            <!-- 活动开始时间 end -->
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'enabled')->dropDownList([
                0 => '不启用',
                1 => '启用',
            ]) ?>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'ad_code')->fileInput() ?>

    <?= Html::img($model->getUploadUrl('ad_code')) ?>


    <?php ActiveForm::end(); ?>

</div>
