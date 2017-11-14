<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="article-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">

        <div class="col-lg-3">
            <?php
                $cats = \common\models\ArticleCat::find()->all();
                $data = [];
                foreach ($cats as $cat) {
                    $data[$cat['cat_id']] = $cat['cat_name'];
                }
                echo $form->field($model, 'cat_id')->widget(Select2::classname(), [
                    'data' => $data,
                    'options' => ['placeholder' => '选择分类'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'article_id') ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'content') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'resource_type')->dropDownList($resourceTypeMap, ['prompt' => '请选择']) ?>
        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'keywords') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'is_open')
                ->dropDownList(
                    Yii::$app->params['is_or_not_map'],
                    ['prompt' => '请选择']
                )
            ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'open_type') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?php
            $data = \common\models\Brand::getBrandListMap();
            echo $form->field($model, 'brand_id')->widget(Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择关联品牌'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order') ?>
        </div>

        <div class="col-lg-3">
            <?php  echo $form->field($model, 'link') ?>
        </div>
    </div>

    <!-- 维度 -->
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'country')
                ->widget(
                    Select2::classname(),
                    [
                        'data' => $countryMap,
                        'options' => ['placeholder' => '请选择关联区域'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]
                );
            ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'link_cat')
                ->widget(
                    Select2::classname(),
                    [
                        'data' => $categoryTree,
                        'options' => ['placeholder' => '请选择关联分类'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]
                );
            ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'scene')->dropDownList($sceneMap, ['prompt' => '请选择']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
