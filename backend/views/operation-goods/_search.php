<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Brand;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsSearch */
/* @var $form yii\widgets\ActiveForm */

$is_or_not_map = Yii::$app->params['is_or_not_map'];
?>

<div class="goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="col-lg-2">
        <?= $form->field($model, 'goods_id') ?>
        <?= $form->field($model, 'goods_name') ?>
    </div>
    <div class="col-lg-2">
        <?= $form->field($model, 'goods_sn') ?>
        <?= $form->field($model, 'cat_id')->dropDownList($cat_id_map, ['prompt' => '请选择分类']) ?>

    </div>
    <div class="col-lg-2">
        <?php  echo $form->field($model, 'brand_id') ?>
        <?php  echo $form->field($model, 'is_on_sale')->dropDownList($is_or_not_map) ?>
    </div>
    <div class="col-lg-2">
        <?php  echo $form->field($model, 'measure_unit') ?>
        <?php  echo $form->field($model, 'goods_brief') ?>
    </div>
    <div class="col-lg-2">
        <?php  echo $form->field($model, 'shelf_life') ?>
        <?php  echo $form->field($model, 'extension_code')->dropDownList(Goods::$extensionCodeMap) ?>
    </div>
    <div class="col-lg-2">
        <br />
        <br />
        <div class="form-group text-center">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>



<?php // echo  $form->field($model, 'goods_name_style') ?>

<?php // echo $form->field($model, 'click_count') ?>

<?php // echo $form->field($model, 'provider_name') ?>

<?php // echo $form->field($model, 'goods_number') ?>

<?php // echo $form->field($model, 'number_per_box') ?>

<?php // echo $form->field($model, 'goods_weight') ?>

<?php // echo $form->field($model, 'market_price') ?>

<?php // echo $form->field($model, 'shop_price') ?>

<?php // echo $form->field($model, 'min_price') ?>

<?php // echo $form->field($model, 'promote_price') ?>

<?php // echo $form->field($model, 'promote_start_date') ?>

<?php // echo $form->field($model, 'promote_end_date') ?>

<?php // echo $form->field($model, 'warn_number') ?>

<?php // echo $form->field($model, 'keywords') ?>

<?php // echo $form->field($model, 'goods_thumb') ?>

<?php // echo $form->field($model, 'goods_img') ?>

<?php // echo $form->field($model, 'original_img') ?>

<?php // echo $form->field($model, 'is_real') ?>

<?php // echo $form->field($model, 'extension_code') ?>

<?php // echo $form->field($model, 'is_alone_sale') ?>

<?php // echo $form->field($model, 'is_shipping') ?>

<?php // echo $form->field($model, 'integral') ?>

<?php // echo $form->field($model, 'add_time') ?>

<?php // echo $form->field($model, 'sort_order') ?>

<?php // echo $form->field($model, 'is_delete') ?>

<?php // echo $form->field($model, 'is_best')->dropDownList($is_or_not_map, ['prompt' => '请选择']) ?>

<?php // echo $form->field($model, 'is_new')->dropDownList($is_or_not_map, ['prompt' => '请选择']) ?>

<?php // echo $form->field($model, 'is_hot')->dropDownList($is_or_not_map, ['prompt' => '请选择']) ?>

<?php // echo $form->field($model, 'is_spec')->dropDownList($is_or_not_map, ['prompt' => '请选择']) ?>

<?php // echo $form->field($model, 'is_promote') ?>

<?php // echo $form->field($model, 'bonus_type_id') ?>

<?php // echo $form->field($model, 'last_update') ?>

<?php // echo $form->field($model, 'goods_type') ?>

<?php // echo $form->field($model, 'seller_note') ?>

<?php // echo $form->field($model, 'give_integral') ?>

<?php // echo $form->field($model, 'rank_integral') ?>

<?php // echo $form->field($model, 'suppliers_id') ?>

<?php // echo $form->field($model, 'is_check') ?>

<?php // echo $form->field($model, 'children') ?>

<?php // echo $form->field($model, 'servicer_strategy_id') ?>
