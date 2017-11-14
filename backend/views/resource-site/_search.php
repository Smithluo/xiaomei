<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ResourceSiteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resource-site-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'site_name') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'site_logo') ?>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
                <?= Html::a(
                        '清空筛选条件',
                        ['index'],
                        [
                            'class' => 'btn btn-warning',
                        ]
                ) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
