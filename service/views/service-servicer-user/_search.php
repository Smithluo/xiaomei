<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'user_name') ?>

    <?= $form->field($model, 'password') ?>

    <?= $form->field($model, 'question') ?>

    <?php // echo $form->field($model, 'answer') ?>

    <?php // echo $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'user_money') ?>

    <?php // echo $form->field($model, 'frozen_money') ?>

    <?php // echo $form->field($model, 'pay_points') ?>

    <?php // echo $form->field($model, 'rank_points') ?>

    <?php // echo $form->field($model, 'address_id') ?>

    <?php // echo $form->field($model, 'zone_id') ?>

    <?php // echo $form->field($model, 'reg_time') ?>

    <?php // echo $form->field($model, 'last_login') ?>

    <?php // echo $form->field($model, 'last_time') ?>

    <?php // echo $form->field($model, 'last_ip') ?>

    <?php // echo $form->field($model, 'visit_count') ?>

    <?php // echo $form->field($model, 'user_rank') ?>

    <?php // echo $form->field($model, 'is_special') ?>

    <?php // echo $form->field($model, 'ec_salt') ?>

    <?php // echo $form->field($model, 'salt') ?>

    <?php // echo $form->field($model, 'parent_id') ?>

    <?php // echo $form->field($model, 'flag') ?>

    <?php // echo $form->field($model, 'alias') ?>

    <?php // echo $form->field($model, 'msn') ?>

    <?php // echo $form->field($model, 'qq') ?>

    <?php // echo $form->field($model, 'office_phone') ?>

    <?php // echo $form->field($model, 'home_phone') ?>

    <?php // echo $form->field($model, 'mobile_phone') ?>

    <?php // echo $form->field($model, 'company_name') ?>

    <?php // echo $form->field($model, 'is_validated') ?>

    <?php // echo $form->field($model, 'credit_line') ?>

    <?php // echo $form->field($model, 'passwd_question') ?>

    <?php // echo $form->field($model, 'passwd_answer') ?>

    <?php // echo $form->field($model, 'headimgurl') ?>

    <?php // echo $form->field($model, 'openid') ?>

    <?php // echo $form->field($model, 'qq_open_id') ?>

    <?php // echo $form->field($model, 'aite_id') ?>

    <?php // echo $form->field($model, 'unionid') ?>

    <?php // echo $form->field($model, 'wx_pc_openid') ?>

    <?php // echo $form->field($model, 'licence_image') ?>

    <?php // echo $form->field($model, 'servicer_info_id') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
