<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Users;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'user_name')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'user_name')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'id_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_name')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_address')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'user_rank')->hiddenInput(['value' => Users::USER_RANK_MEMBER])->label('') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'user_type')->hiddenInput(['value' => Users::USER_TYPE_SERVICER])->label('') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'is_checked')->hiddenInput(['value' => Users::IS_CHECKED_STATUS_PASSED])->label('') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('更新', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
