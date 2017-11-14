<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsSupplyInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-supply-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?php
            echo $form->field($model, 'goods_id')->widget(kartik\select2\Select2::className(), [
                'data' => \backend\models\Goods::getGoodsMap(),
                'options' => ['placeholder' => '输入商品ID和商品名称查询'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('输入商品ID和商品名称查询');
            ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'goods_sn') ?>
        </div>
        <div class="col-lg-2">
            <?=
            $form->field($model, 'brand_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \common\models\Brand::getBrandListMap(),
                'options' => ['placeholder' => '选择品牌'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('搜索品牌名');
            ?>
        </div>
        <div class="col-lg-2">
            <?php echo $form->field($model, 'is_on_sale')->dropDownList(Yii::$app->params['is_or_not_map'], ['prompt' => '显示全部']) ?>
        </div>
        <div class="col-lg-2">
            <?php echo $form->field($model, 'extension_code')->dropDownList(Goods::$extensionCodeMap, ['prompt' => '显示全部']) ?>
        </div>
        <div class="col-lg-2">
            <?php echo $form->field($model, 'is_delete')->dropDownList(Goods::$is_delete_map, ['prompt' => '显示全部']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
