<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $isCreate ?
        $form->field($model, 'user_name')->textInput(['maxlength' => true]) : $form->field($model, 'user_name')->textInput(['maxlength' => true, 'readonly'=>'readonly'])
    ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'licence_image')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'user_rank')->hiddenInput(['value' => Users::USER_RANK_MEMBER])->label('') ?>
    <?= $form->field($model, 'user_type')->hiddenInput(['value' => Users::USER_TYPE_SERVICER])->label('') ?>
    <?= $form->field($model, 'is_checked')->hiddenInput(['value' => Users::IS_CHECKED_STATUS_PASSED])->label('') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
