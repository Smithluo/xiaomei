<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Brand;

/* @var $this yii\web\View */
/* @var $model backend\models\Brand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-8\">{input}</div>\n
                <div class=\"col-lg-4\"></div>
                <div class=\"col-lg-8\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label text-right'],
        ],
    ]); ?>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => [
            [
                'label' => '属性编辑',
                'content' => $this->render('_form_general', ['model' => $model, 'form' => $form, 'shippingList' => $shippingList, 'servicerStrategy' => $servicerStrategy, 'touchBrand' => isset($touchBrand) ? $touchBrand : null]),
                'active' => true
            ],
            [
                'label' => '品牌详情',
                'content' => $this->render('_form_content', ['model' => $model, 'form' => $form, 'touchBrand' => isset($touchBrand) ? $touchBrand : null]),
            ],
            [
                'label' => '品牌授权',
                'content' => $this->render('_form_license', ['model' => $model, 'form' => $form, 'touchBrand' => isset($touchBrand) ? $touchBrand : null]),
            ],
            [
                'label' => '品牌进口资质',
                'content' => $this->render('_form_qualification', ['model' => $model, 'form' => $form, 'touchBrand' => isset($touchBrand) ? $touchBrand : null]),
            ],
        ]]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
