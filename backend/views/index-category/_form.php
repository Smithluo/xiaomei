<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-zhifa-form">
    tips:
    <h4 style="color:red;">品牌分类所对应的id   | 护肤 => 1 | 彩妆 => 2 | 个人狐狸 => 3 | 家庭护理 => 4 | 母婴专区 => 5 | 男士专区 => 6 | 香氛 => 7 | 面膜 => 12 |</h4>
     <p>链接格式为 /default/category/topic/cat_id/(这里配置id).html</p>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="row">

        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-2">
            <?= $form->field($model, 'm_url')->textInput(['maxlength' => true,'placeHolder'=>'/default/category/index/cat_id/']) ?>
        </div>

        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'logo')->fileInput() ?>
        </div>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
