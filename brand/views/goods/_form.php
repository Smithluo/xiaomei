<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-form">

    <?php $form = ActiveForm::begin([]); ?>

<!--    --><?php //echo $form->field($model, 'goods_name')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'goods_number')->textInput() ?>

    <?= $form->field($model, 'warn_number')->textInput() ?>

    <?= $form->field($model, 'cat_id')->dropDownList([$model->cat_id => $model->category->cat_name]) ?>

    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'start_num')->textInput(['maxlength' => true, 'readonly' => true]) ?>

<!--    --><?php //$form->field($model, 'goods_name_style')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'click_count')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'brand_id')->textInput([$model->brand_id => $model->brand->brand_name]) ?>

<!--    --><?php //$form->field($model, 'provider_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'measure_unit')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'number_per_box')->textInput(['readonly' => true]) ?>

<!--    --><?php //$form->field($model, 'goods_weight')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo  $form->field($model, 'market_price')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'shop_price')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'min_price')->textInput(['maxlength' => true, 'readonly' => true]) ?>

<!--    --><?php //$form->field($model, 'promote_price')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'promote_start_date')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'promote_end_date')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'goods_brief')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'goods_desc')->textarea(['rows' => 6]) ?>

<!--    --><?php //$form->field($model, 'goods_thumb')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'goods_img')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'original_img')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'is_real')->textInput() ?>

<!--    --><?php //$form->field($model, 'extension_code')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'is_on_sale')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_alone_sale')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_shipping')->textInput() ?>

<!--    --><?php //$form->field($model, 'integral')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo  $form->field($model, 'add_time')->textInput([
//        'maxlength' => true,
//        'value' => date('Y-m-d H:i:s', $model->last_update),
//        'readonly' => true
//    ]) ?>

<!--    --><?php //$form->field($model, 'sort_order')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_delete')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_best')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_new')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_hot')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_spec')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_promote')->textInput() ?>

<!--    --><?php //$form->field($model, 'bonus_type_id')->textInput() ?>

    <?php /*echo $form->field($model, 'last_update')->textInput([
        'maxlength' => true,
        'value' => date('Y-m-d H:i:s', $model->last_update),
        'readonly' => true
    ]) */?>

<!--    --><?php //$form->field($model, 'goods_type')->textInput() ?>

<!--    --><?php //$form->field($model, 'seller_note')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'give_integral')->textInput() ?>

<!--    --><?php //$form->field($model, 'rank_integral')->textInput() ?>

<!--    --><?php //$form->field($model, 'suppliers_id')->textInput() ?>

<!--    --><?php //$form->field($model, 'is_check')->textInput() ?>

<!--    --><?php //$form->field($model, 'children')->textInput(['maxlength' => true]) ?>

<!--    --><?php //$form->field($model, 'shelf_life')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
