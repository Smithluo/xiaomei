<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\EventRule;

/* @var $this yii\web\View */
/* @var $model common\models\EventRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'rule_id') ?>

    <?= $form->field($model, 'match_type')->dropDownList(EventRule::$match_type_map) ?>

    <?= $form->field($model, 'match_value') ?>

    <?= $form->field($model, 'match_effect')->dropDownList(EventRule::$match_effect_map) ?>

    <?= $form->field($model, 'gift_id') ?>

    <?= $form->field($model, 'gift_num') ?>

    <?= $form->field($model, 'gift_show_peice') ?>

    <?php // echo $form->field($model, 'gift_need_pay') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
