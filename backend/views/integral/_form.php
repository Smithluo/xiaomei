<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Integral */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integral-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-3\">{input}</div>\n
                <div class=\"col-lg-3\">{error}</div>
                <div class=\"col-lg-5\">{hint}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
        ],
    ]); ?>

    <?= $form->field($model, 'integral')->textInput(['maxlength' => true, 'placeholder' => '正数为收益，负数为消费']) ?>

    <?php
    $usersModels = \common\models\Users::find()->select([
        'user_id',
        'user_name',
        'mobile_phone',
    ])->where([
        'is_checked' => 2,
    ])->andWhere('mobile_phone > 0')->asArray()->all();

    $data = [];
    foreach ($usersModels as $userModel) {
        $data[$userModel['user_id']] = $userModel['user_id']. '----'. $userModel['user_name']. '----'. $userModel['mobile_phone'];
    }

    echo $form->field($model, 'user_id')->widget(kartik\widgets\Select2::className(), [
        'data' => $data,
        'options' => [
            'placeholder' => '选择用户',
        ],
    ])
    ?>

    <?= $form->field($model, 'pay_code')->dropDownList($payCodeMap) ?>

    <?= $form->field($model, 'out_trade_no')->textInput(['maxlength' => true, 'placeholder' => '填写备注']) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'placeholder' => '不要前些纯数字，避免与实际订单ID冲突']) ?>

    <?= $form->field($model, 'status')->dropDownList($statusMap) ?>

    <?php
        if ($model->isNewRecord) {
            echo $form->field($model, 'created_at')
                ->hiddenInput(['value' => DateTimeHelper::getFormatGMTTimesTimestamp()])
                ->label('');
        }

        echo $form->field($model, 'updated_at')
            ->hiddenInput(['value' => DateTimeHelper::getFormatGMTTimesTimestamp()])
            ->label('');;
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
