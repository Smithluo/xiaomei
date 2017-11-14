<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cat_id')->textInput() ?>

    <?= $form->field($model, 'attr_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'attr_input_type')->textInput() ?>

    <?= $form->field($model, 'attr_type')->textInput() ?>

    <?= $form->field($model, 'attr_values')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'attr_index')->textInput() ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'is_linked')->textInput() ?>

    <?= $form->field($model, 'attr_group')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
