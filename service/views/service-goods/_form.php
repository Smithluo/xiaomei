<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model service\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cat_id')->textInput() ?>

    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_name_style')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'click_count')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_id')->textInput() ?>

    <?= $form->field($model, 'provider_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_number')->textInput() ?>

    <?= $form->field($model, 'measure_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_per_box')->textInput() ?>

    <?= $form->field($model, 'goods_weight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'min_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promote_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promote_start_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promote_end_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'warn_number')->textInput() ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_brief')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'goods_thumb')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_real')->textInput() ?>

    <?= $form->field($model, 'extension_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_on_sale')->textInput() ?>

    <?= $form->field($model, 'is_alone_sale')->textInput() ?>

    <?= $form->field($model, 'is_shipping')->textInput() ?>

    <?= $form->field($model, 'integral')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'is_delete')->textInput() ?>

    <?= $form->field($model, 'is_best')->textInput() ?>

    <?= $form->field($model, 'is_new')->textInput() ?>

    <?= $form->field($model, 'is_hot')->textInput() ?>

    <?= $form->field($model, 'is_spec')->textInput() ?>

    <?= $form->field($model, 'is_promote')->textInput() ?>

    <?= $form->field($model, 'bonus_type_id')->textInput() ?>

    <?= $form->field($model, 'last_update')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_type')->textInput() ?>

    <?= $form->field($model, 'seller_note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'give_integral')->textInput() ?>

    <?= $form->field($model, 'rank_integral')->textInput() ?>

    <?= $form->field($model, 'suppliers_id')->textInput() ?>

    <?= $form->field($model, 'is_check')->textInput() ?>

    <?= $form->field($model, 'children')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shelf_life')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'servicer_strategy_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
