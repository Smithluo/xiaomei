<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use backend\models\Brand;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '品牌管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-index">
<p>tips:首页新上品牌    是以创建品牌的时间排序的  不是按你改为显示的时间</p>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建品牌', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('导出品牌', ['export'], ['class' => 'btn btn-success']) ?>
    </p>

        <div class="row">
            <div class="col-lg-3">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'action' => ['import-discount'],
                    'method' => 'post',
                    'options' => ['enctype' => 'multipart/form-data'
                    ]])
                ?>

                <?= $form->field($discountImportModel, 'file')->fileInput() ?>

                <button>提交</button>

                <?php \yii\widgets\ActiveForm::end() ?>


            </div>
            <div class="col-lg-3">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'action' => ['upload-country'],
                    'method' => 'post',
                    'options' => ['enctype' => 'multipart/form-data'
                    ]])
                ?>
                <?= $form->field($uploadCountry, 'icon')->fileInput() ?>

                <button>提交</button>

                <?php \yii\widgets\ActiveForm::end() ?>
            </div>
        </div>




    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],

        'brand_id',
        'brand_name',
        'brand_depot_area',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'character',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/brand/editValue'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'main_cat',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/brand/editValue'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
//            'brand_logo',
//            'brand_logo_two',
        // 'brand_policy',
//             'brand_desc:ntext',
//             'brand_desc_long:ntext',
        'short_brand_desc',
        'sort_order',
        [
            'attribute' => 'is_show',
            'format' => 'html',
            'value' => function($model){
                return Brand::$is_show_icon_map[$model->is_show];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'is_hot',
            'format' => 'html',
            'value' => function($model){
                return Brand::$is_show_icon_map[$model->is_hot];
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'md',
                    'inputType' => 'dropDownList',
                    'data' => Brand::$is_hot_map,
                    'formOptions' => [
                        'action' => ['/brand/editValue'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'brand_tag',
        [
            'attribute' => 'servicer_strategy_id',
            'value' => function($model){
                return empty($model->servicerStrategy) ? '(未设置)' : $model->servicerStrategy->percent_total;
            }
        ],
        [
            'attribute' => 'supplier_user_id',
            'value' => function($model){
                return empty($model->supplierUser) ? null: $model->supplierUser->showName. '('. $model->supplierUser->company_name. ')';
            }
        ],
        [
            'attribute' => 'discount',
            'value' => function($model){
                return $model->discount ?: '(未设置)';
            }
        ],
        [
            'attribute' => 'shipping_id',
            'value' => function($model) {
                if (!$model->shipping) {
                    return '';
                }
                return $model->shipping->shipping_name;
            }
        ],
        'turn_show_time',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'brand_area',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '区域',
                    'size' => 'sm',
                    'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => Brand::$brand_area_map,
                        'options' => [
                            'placeholder' => '选择分类',
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ],
                    ],
                    'formOptions' => [
                        'action' => ['/brand/editValue'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'label' => '经营品类',
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'catId',
            'value' => function ($model) {
                $brand = \common\models\BrandCat::find()->joinWith([
                    'category'
                ])->where([
                    'brand_id' => $model->brand_id,
                ])->asArray()->all();
                $result = '';
                foreach ($brand as $brandStr) {
                    foreach ($brandStr['category'] as $categoryStr)
                    $result .= ''. $categoryStr['cat_name']. ',';
                }
                if (!empty($result)) {
                    return substr($result, 0, -1);
                } else {
                    return '';
                }
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '分类',
                    'size' => 'sm',
                    'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => \common\models\Category::getTopCatMap(),
                        'options' => [
                            'placeholder' => '选择分类',
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'multiple' => true,
                        ],
                    ],
                    'formOptions' => [
                        'action' => ['/brand/editValue'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'country',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}'
        ],
    ];
    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">品牌列表</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-brand',
        ],
    ]); ?>
</div>
