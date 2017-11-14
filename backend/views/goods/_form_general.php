<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\UserRank;
use common\helper\CacheHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$is_on_sale_map = Goods::$is_on_sale_map;
$is_delete_map = Goods::$is_delete_map;
$buy_by_box_map = Goods::$buy_by_box_map;
$discount_enable_map = Goods::$discount_enable_map;
$category_list = CacheHelper::getGoodsCategoryCache('cat_map');
$brandMap = Brand::getBrandIdNameMap();
?>
<div class="col-lg-3">
    <?= $form->field($model, 'cat_id')->dropDownList($category_list) ?>

    <?= $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $brandMap,
            'options' => ['placeholder' => '选择所属品牌'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'need_rank')->dropDownList(UserRank::$user_rank_map)?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'measure_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shelf_life')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_brief')->textInput(['maxlength' => true]) ?>
</div>
<div class="col-lg-3">
    <?= $form->field($model, 'is_on_sale')->dropDownList($is_on_sale_map) ?>

    <?= $form->field($model, 'discount_disable')->dropDownList($discount_enable_map) ?>

    <?php
        $brandShipping = empty($model->brand->shipping->shipping_name) ? '' : $model->brand->shipping->shipping_name;

        $shippingItems = [0 => '跟随当前品牌配送方式:'. $brandShipping];
        foreach ($shippingList as $shipping) {
            $shippingItems[$shipping->shipping_id] = $shipping->shipping_name;
        }
        echo $form->field($model, 'shipping_id')->dropDownList($shippingItems);
    ?>

    <?php
        $supplierMap = [];
        foreach ($suppliers as $supplier) {
            $supplierMap[$supplier['user_id']] = $supplier['user_name']. '('. $supplier['company_name']. ')';
        }
        echo $form->field($model, 'supplier_user_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $supplierMap,
            'options' => ['placeholder' => '选择供应商'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?= $form->field($model, 'buy_by_box')->dropDownList($buy_by_box_map) ?>

<!--    --><?php //echo $form->field($model, 'goods_weight')->textInput(['maxlength' => true]) ?>
    <?php // echo $form->field($model, 'extension_code')->dropDownList(Goods::$extensionCodeMap) ?>
    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'certificate')->textInput() ?>

    <?= $form->field($model, 'seller_note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_delete')->dropDownList($is_delete_map) ?>
</div>
