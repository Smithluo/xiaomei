<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\KnowledgeShowBrandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="knowledge-show-brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
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

    <div class="row">
        <!--<div class="col-lg-3">
            <?php /*// echo $form->field($model, 'id') */?>
        </div>-->

        <div class="col-lg-3">
            <?= $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $brandMap,
                'options' => ['placeholder' => '选择品牌'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order') ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'platform')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $platformMap,
                'options' => ['placeholder' => '选择平台'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>

        <div class="col-lg-3">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
