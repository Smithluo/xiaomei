<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Brand;

/* @var $this yii\web\View */
/* @var $model common\models\TouchArticleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touch-article-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'article_id') ?>
        </div>
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
            <?php  echo $form->field($model, 'is_open')->dropDownList(
                Yii::$app->params['is_or_not_map'],
                ['prompt' => '请选择']
            )?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'title') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'resource_type')->dropDownList($resourceTypeMap, ['prompt' => '请选择']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'content') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'author') ?>
        </div>
        <div class="col-lg-3">
            <?php
            $data = Brand::getBrandListMap();
            echo $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择关联品牌'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'author_email') ?>

    <?php // echo $form->field($model, 'keywords') ?>

    <?php // echo $form->field($model, 'add_time') ?>

    <?php // echo $form->field($model, 'file_url') ?>

    <?php // echo $form->field($model, 'open_type') ?>

    <?php // echo $form->field($model, 'link') ?>

    <?php // echo $form->field($model, 'description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
