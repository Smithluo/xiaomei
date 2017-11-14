<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SeasonGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-goods-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord) :?>
    <?php for($i=0; $i < 5 ;$i++ ):?>
        <div class="row">

            <div class="col-lg-1">
                <?= $form->field($model, "[$i]type")->dropDownList(
                        \common\models\SeasonGoods::Type()
                ) ?>
            </div>
            <div class="col-lg-1">
                <?= $form->field($model, "[$i]name")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "[$i]desc")->textarea() ?>
            </div>

            <div class="col-lg-3">
                <?php echo $form->field($model, "[$i]goods_id")->widget(kartik\widgets\Select2::className(), [
                    'data' => \common\models\SeasonGoods::Goods(),
                    'options' => [
                        'multiple' => false,
                        'prompt' => '请选择商品'
                    ],
                ]) ?>
            </div>

            <div class="col-lg-1">
                <?= $form->field($model, "[$i]sort_order")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "[$i]is_show")->radioList(\common\models\SeasonGoods::$is_show_map) ?>
            </div>
        </div>
    <?php endfor;?>
        <?php else:?>
        <div class="row">

            <div class="col-lg-2">
                <?= $form->field($model, "name")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "desc")->textInput() ?>
            </div>

            <div class="col-lg-3">
            <?php echo $form->field($model, "goods_id")->widget(kartik\widgets\Select2::className(), [
                'data' => \common\models\SeasonGoods::Goods(),
                'options' => [
                    'multiple' => false,
                    'prompt' => '请选择商品'
                ],
            ]) ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "sort_order")->textInput() ?>
            </div>
        </div>
    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
