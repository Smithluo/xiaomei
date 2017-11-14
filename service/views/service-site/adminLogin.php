<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->params['steel_boot'] = 'app/service/login';
?>


<?php $form = ActiveForm::begin([
        'action' => '/service-site/admin-login',
        'method' => 'post',
        'enableClientScript' => false,
        'fieldClass' => 'common\widgets\ActiveField',
    ]); ?>

    <?php
    $users = \common\models\Users::find()->where([
        'not',
        ['servicer_info_id' => 0]
    ])->all();

    $data = [];
    foreach ($users as $user) {
        $data[$user->user_id] = $user->showName. ' | '. $user->company_name;
    }

    echo $form->field($model, 'servicerUserId')->dropDownList($data);
    ?>

    <?= $form->field($model, 'username', [
        'template'=>'
                            <div class="form-group">
                                <i class="uname"></i>
                                {input}
                            </div>',
        'inputOptions'=>['placeholder'=>'请输入账号名', 'class'=>'form-control', 'autocomplete'=>'off'],
    ]) ?>

    <?= $form->field($model, 'password', [
        'template' => '
                <div class="form-group">
                    <i class="psd"></i>
                    {input}
                </div>
        ',
        'inputOptions'=>['placeholder'=>'请输入密码', 'class'=>'form-control'],
    ])->passwordInput() ?>

    <div class="txt">
        <label class="tips"><?php
            if($model->errors) {
                $errText = '';
                $isFirst = true;
                foreach($model->errors as $error) {
                    if($isFirst) {
                        $errText .= $error[0];
                    }
                    else {
                        $errText .= ','.$error[0];
                    }
                    $isFirst = false;
                }
                echo $errText;
            }
            ?></label>
        <?= Html::submitButton('登录', ['class' => 'btn', 'name' => 'login-button']) ?>
    </div>

<?php ActiveForm::end(); ?>
