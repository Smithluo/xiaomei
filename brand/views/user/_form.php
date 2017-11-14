<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'newpassword')->passwordInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'repassword')->passwordInput(['maxlength' => true]) ?>

    <div class="wrapper wrapper-content animated fadeInRight">
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
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal">
                            <div class="form-group"><label class="col-sm-2 control-label">原密码</label>

                                <div class="col-sm-10"><input type="password" class="form-control"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group"><label class="col-sm-2 control-label">新密码</label>
                                <div class="col-sm-10"><input type="password" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group"><label class="col-sm-2 control-label">确认新密码</label>
                                <div class="col-sm-10"><input type="password" class="form-control" name="password"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">确认修改</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('确认修改', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

<div class="footer">
    <div>
        <strong>Copyright</strong> 小美诚品 &copy; <?=date('Y')?>
    </div>
</div>