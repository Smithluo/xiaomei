<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10 0010
 * Time: 21:58
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Brand;
use common\helper\DateTimeHelper;

?>

<div class="col-lg-6">
    <?= $form->field($model, 'brand_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_depot_area')->textInput(['maxlength' => true]) ?>

    <?php
    //    echo $form->field($model, 'brand_logo')->fileInput(['maxlength' => true]);
    //    echo $form->field($model, 'brand_logo_two')->fileInput(['maxlength' => true]);
    //    echo $form->field($model, 'brand_policy')->fileInput(['maxlength' => true])->label('品牌政策');
    ?>

    <?= $form->field($model, 'short_brand_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_desc')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'character')->textInput() ?>

    <?= $form->field($model, 'is_show')->radioList(Brand::$is_show_map) ?>

    <?= $form->field($model, 'is_hot')->radioList(Brand::$is_show_map) ?>

    <?= $form->field($model, 'discount')->textInput([
        'placeholder' => '例如：5.5',
    ]) ?>

    <?= $form->field($model, 'country')->textInput() ?>

    <?= $form->field($servicerStrategy, 'percent_total')->textInput() ?>

    <div class="col-lg-2">
        <?= $form->field($model, 'brand_logo')->fileInput(['accept' => 'image/*']) ?>
    </div>

    <div class="col-lg-4">
        <!-- Original image -->
        <?= Html::img($model->getUploadUrl('brand_logo'), ['class' => 'img-thumbnail']) ?>
    </div>

    <div class="col-lg-2">
        <?= $form->field($model, 'brand_logo_two')->fileInput(['accept' => 'image/*']) ?>
    </div>

    <div class="col-lg-4">
        <!-- Original image -->
        <?= Html::img($model->getUploadUrl('brand_logo_two'), ['class' => 'img-thumbnail']) ?>
    </div>

    <?= $form->field($model, 'turn_show_time')->hiddenInput(['value' => gmdate('Y-m-d H:i:s', DateTimeHelper::getFormatGMTTimesTimestamp(time()))])->label('') ?>
</div>

<div class="col-lg-6">
    <?= $form->field($model, 'site_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?php
    $albums = \common\models\WechatAlbum::find()->asArray()->all();
    $data = [];
    foreach ($albums as $album) {
        $data[$album['album_id']] = $album['album_name']. '['. $album['image_width']. 'x'. $album['image_height']. ']';
    }
    echo $form->field($model, 'album_id')->widget(kartik\widgets\Select2::className(), [
        'data' => $data,
        'options' => ['placeholder' => '选择相册'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'brand_tag')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_desc_long')->textarea(['rows' => 6]) ?>

    <?php
    $shippingItems = [];
    foreach ($shippingList as $shipping) {
        $shippingItems[$shipping->shipping_id] = $shipping->shipping_name;
    }
    echo $form->field($model, 'shipping_id')->dropDownList($shippingItems);
    ?>

    <div class="col-lg-2">
        <?= $form->field($touchBrand, 'brand_banner')->fileInput(['accept' => 'image/*']) ?>
    </div>

    <div class="col-lg-4">
        <!-- Original image -->
        <?= Html::img($touchBrand->getUploadUrl('brand_banner'), ['class' => 'img-thumbnail']) ?>
    </div>

</div>

<div class="col-lg-4">
    <?= $form->field($model, 'main_cat')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brand_area')->dropDownList(Brand::$brand_area_map)?>
    <?= $form->field($model, 'brandCatIds')->dropDownList(\common\models\Category::getTopCatMap())?>
</div>