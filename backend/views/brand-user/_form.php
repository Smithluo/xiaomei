<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Users;

/* @var $this yii\web\View */
/* @var $model common\models\BrandUser */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="brand-user-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-4\"></div><div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label'],  //修改label的样式
        ],
    ]); ?>

    <?php
        if ($model->isNewRecord) {
            echo $form->field($model, 'user_id')->hiddenInput()->label('');
        }
    ?>

    <div class="col-lg-3">
        <h2>品牌商账号信息</h2>
        <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

        <?= $model->isNewRecord ? $form->field($model, 'password')->passwordInput(['maxlength' => true]) : '' ?>

        <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'user_rank')->hiddenInput(['value' => Users::USER_RANK_REGISTED])->label('') ?>
        <?= $form->field($model, 'user_type')->hiddenInput(['value' => Users::USER_TYPE_SUPPLIER])->label('') ?>
        <?= $form->field($model, 'is_checked')->hiddenInput(['value' => Users::IS_CHECKED_STATUS_PASSED])->label('') ?>

    </div>
    <div class="col-lg-3">
        <h2>品牌商账号信息</h2>
        <?= $form->field($model, 'mobile_phone')->textInput([
            'maxlength' => true,
            'placeholder' => '移动电话可用于登录品牌商后台和身份验证',
        ]) ?>

        <?= $form->field($model, 'office_phone')->textInput([
            'maxlength' => true,
            'placeholder' => '如果没有，可以不填',
        ]) ?>

        <?= $form->field($model, 'email')->textInput([
            'maxlength' => true,
            'placeholder' => '请填写有效的邮箱',
        ]) ?>
        <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-3">
        <h2>银行账号信息</h2>
        <?php if ($bankModel) : ?>
            <?= $form->field($bankModel, 'user_name')->textInput(['maxLength' => true])->label('开户名') ?>

            <?= $form->field($bankModel, 'id_card_no')->textInput(['maxLength' => true]) ?>

            <?= $form->field($bankModel, 'bank_name')->textInput(['maxLength' => true]) ?>

            <?= $form->field($bankModel, 'bank_card_no')->textInput(['maxLength' => true]) ?>

            <?= $form->field($bankModel, 'bank_address')->textInput(['maxLength' => true]) ?>
        <?php endif;?>
    </div>
    <div class="col-lg-3">
        <h2>品牌商联系人信息</h2>
        <?php if ($brandAdminModel) : ?>
            <?= $form->field($brandAdminModel, 'linkman')->textInput(['maxLength' => true])->label('品牌对接人') ?>

            <?= $form->field($brandAdminModel, 'mobile')->textInput(['maxLength' => true])->label('对接人联系方式') ?>

            <?= $form->field($brandAdminModel, 'back_address')->textInput([
                'maxLength' => true,
                'placeholder' => '省市县区 街道 楼层 房间号',
            ]) ?>
        <?php endif;?>
    </div>
    <div class="col-lg-12">
        <?= $form->field($model, 'brand_id_list', [
            'template' => "{label}\n<div class=\"col-lg-11\">{input}</div>\n",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],  //修改label的样式
        ])->checkboxList($brand_map, [
            'item' => function($index, $label, $name, $checked, $value) {
                $checked=$checked?"checked":"";
                $return = '<div class="md-checkbox col-lg-2">';
                $return .= '<input type="checkbox" id="' . $name . $value . '" name="' . $name . '" value="' . $value . '" class="md-checkbox" '.$checked.'>';
                $return .= '<label for="' . $name . $value . '">
                    <span></span>
                    <span class="check"></span>
                    <span class="box"></span>' . ucwords($label) . '</label>';
                $return .= '</div>';
                return $return;
            }
        ]) ?>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
