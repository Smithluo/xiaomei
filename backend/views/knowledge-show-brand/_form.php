<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\KnowledgeShowBrand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="knowledge-show-brand-form">

    <?php $form = ActiveForm::begin([
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
        <div class="col-lg-4">
            <?= $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $brandMap,
                'options' => ['placeholder' => '选择品牌'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>

            <?= $form->field($model, 'platform')->dropDownList($platformMap, ['prompt' => '请选择']) ?>

            <?= $form->field($model, 'sort_order')->textInput(['prompt' => 30000]) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <div class="col-lg-4">

        </div>

        <div class="col-lg-4">

        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
