<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '修改密码';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/userMange';

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/modifyPsd/modifyPsd.css?version='.$r_version.'" type="text/css" rel="stylesheet">
';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/grid.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/modifyPsd/modifyPsd.js?version='.$r_version.'"></script>
<script>steel.boot(\'app/statement/statement\');</script>;';
?>
<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>修改密码</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display: block;">

                    <?php
                    $form = ActiveForm::begin([
                        'method' => 'post',
                        'id' => 'change-password-form',
                        'enableClientScript' => false,
                        'fieldClass' => 'common\widgets\ActiveField',
                        'options' => [
                            'class' => 'form-inline',
                        ],
                        'fieldConfig' => [  //统一修改字段的模板
                            'template' => "{label}\n<div class=\"col-lg-3\" >{input}</div>\n{hint}\n<div class=\"col-lg-3\">{error}</div><br />",
                            'labelOptions' => ['class' => 'col-sm-2 control-label text-right'],  //修改label的样式
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'password_old', [
                        /*'template'=>'
                            <div class="form-group">{label}
                                    <div class="col-sm-10">{input}</div>
                                    {hint}{error}
                                </div>',
                        'labelOptions'=>['class'=>'col-sm-2 control-label'],*/
                        'inputOptions'=>['class'=>'form-control'],
                    ])->passwordInput(['autofocus' => true]) ?>
                    <div class="hr-line-dashed"></div>
                    <?= $form->field($model, 'password', [
                        /*'template'=>'
                            <div class="form-group">{label}
                                    <div class="col-sm-10">{input}</div>
                                    {hint}{error}</div>
                                </div>',
                        'labelOptions'=>['class'=>'col-sm-2 control-label'],*/
                        'inputOptions'=>['class'=>'form-control'],
                    ])->passwordInput() ?>
                    <div class="hr-line-dashed"></div>
                    <?= $form->field($model, 'password_repeat', [
                        /*'template'=>'
                            <div class="form-group">{label}
                                    <div class="col-sm-10">{input}</div>
                                    {hint}{error}
                                </div>',
                        'labelOptions'=>['class'=>'col-sm-2 control-label'],*/
                        'inputOptions'=>['class'=>'form-control'],
                    ])->passwordInput() ?>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <?= Html::submitButton('确认修改', ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>