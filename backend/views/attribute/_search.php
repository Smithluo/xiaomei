<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AttributeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attribute-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'attr_id') ?>

    <?= $form->field($model, 'cat_id') ?>

    <?= $form->field($model, 'attr_name') ?>

    <?= $form->field($model, 'attr_input_type') ?>

    <?= $form->field($model, 'attr_type') ?>

    <?php // echo $form->field($model, 'attr_values') ?>

    <?php // echo $form->field($model, 'attr_index') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'is_linked') ?>

    <?php // echo $form->field($model, 'attr_group') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
