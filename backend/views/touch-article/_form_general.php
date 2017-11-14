<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 9:15
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;

?>

<div class="row">
    <div class="col-lg-3">
        <?php
        $cats = \common\models\TouchArticleCat::find()->all();
        $data = [];
        foreach ($cats as $cat) {
            $data[$cat['cat_id']] = $cat['cat_name'];
        }
        echo $form->field($model, 'cat_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => '选择分类'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'tag')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-3">
        <?php  echo $form->field($model, 'resource_type')->dropDownList($resourceTypeMap, ['prompt' => '请选择']) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'is_open')->dropDownList([
            0 => '不显示',
            1 => '显示',
        ]) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">

    <div class="col-lg-3">
        <?= $form->field($model, 'type')->dropDownList([
            0 => '普通',
            1 => '广告',
        ]) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'sort_order')->textInput() ?>
    </div>

    <div class="col-lg-3">
        <?php
        $brandList = \backend\models\Brand::findAll(['is_show' => 1]);
        $data = [];
        foreach ($brandList as $brand) {
            $data[$brand->brand_id] = $brand->brand_name;
        }
        echo $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => '选择品牌'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'author_email')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-2">
        <?= $form->field($model, 'pic')->fileInput(['accept' => 'image/*']) ?>
    </div>

    <div class="col-lg-4">
        <!-- Original image -->
        <?= Html::img($model->getUploadUrl('pic'), ['class' => 'img-thumbnail']) ?>
    </div>

    <div class="col-lg-3">
        <?php
        echo $form->field($model, 'gallery_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $galleryMap,
            'options' => ['placeholder' => '请选择关联相册'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($model, 'resource_site_id')->dropDownList($resourceSiteMap, ['prompt' => '请选择']) ?>
    </div>

</div>

<div class="row">
    <div class="col-lg-12">
        <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'rows' => 6]) ?>
    </div>
</div>

