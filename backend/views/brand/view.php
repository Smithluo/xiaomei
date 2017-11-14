<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Brand */

$this->title = $model->brand_name;
$this->params['breadcrumbs'][] = ['label' => '品牌详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="brand-view">

    <?php
    if (!$model->isNewRecord) {
        echo Html::a('PC站预览', 'http://www.xiaomei360.com/brand_preview.php?id='. $model->brand_id, [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);

        echo Html::a('微信站预览', 'http://m.xiaomei360.com/default/brand/preview/id/'. $model->brand_id.'.html', [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);
    }
    ?>

    <?php

    $shippingData = \common\models\Shipping::find()->where(['enabled' => 1])->asArray()->all();
    $shippingData = array_column($shippingData, 'shipping_name', 'shipping_id');

    $attributes = [
        [
            'group' => true,
            'label' => '基础信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'brand_name',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute'=>'site_url',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'attribute' => 'brand_depot_area',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'short_brand_desc',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute'=>'brand_desc',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'attribute' => 'is_show',
                    'value' => $model->is_show ? '是' : '否',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type'=>DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => '是',
                            'offText' => '否',
                        ]
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'discount',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'country',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 20%'
                    ],
                ],
                [
                    'attribute' => 'sort_order',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'brand_tag',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute'=>'brand_desc_long',
                    'type' => DetailView::INPUT_TEXTAREA,
                    'options'=>['rows'=>5],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'shipping_id',
                    'value' => empty($model->shipping) ? '' : $model->shipping->shipping_name,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => $shippingData,
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'width' => '100%',
                        ],
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'percent_total',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'viewModel' => $servicerStrategy,
                    'editModel' => $servicerStrategy,
                ],
                [
                    'attribute' => 'brand_logo',
                    'format' => 'raw',
                    'value' => Html::img(\common\helper\ImageHelper::get_image_path($model->brand_logo)),
                    'type' => DetailView::INPUT_FILE,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'brand_logo_two',
                    'format' => 'raw',
                    'value' => Html::img(\common\helper\ImageHelper::get_image_path($model->brand_logo_two)),
                    'type' => DetailView::INPUT_FILE,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'event_id',
                    'value' => empty($model->event) ? 0: '('. $model->event->event_id. ')'. $model->event->event_name,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => $couponEventList,
                        'options' => ['placeholder' => '选择参与的优惠券活动'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                    ],
                ],
                [
                    'attribute' => 'brand_banner',
                    'format' => 'raw',
                    'value' => Html::img(\common\helper\ImageHelper::get_image_path($model->touchBrand->brand_banner), ['style' => 'height: 150px']),
                    'type' => DetailView::INPUT_FILE,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'editModel' => $model->touchBrand,
                    'viewModel' => $model->touchBrand,
                ],
                [
                    'attribute' => 'top_touch_ad_position_id',
                    'value' => empty(\common\models\TouchAdPosition::getTouchAdPositionList()[$model->top_touch_ad_position_id]) ? '': \common\models\TouchAdPosition::getTouchAdPositionList()[$model->top_touch_ad_position_id],
                    'type' => DetailView::INPUT_SELECT2,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'widgetOptions' => [
                        'data' => \common\models\TouchAdPosition::getTouchAdPositionList(),
                        'options' => ['placeholder' => '选择微信中部广告位'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                    ],
                ],
                [
                    'attribute' => 'center_touch_ad_position_id',
                    'value' => empty(\common\models\TouchAdPosition::getTouchAdPositionList()[$model->center_touch_ad_position_id]) ? '': \common\models\TouchAdPosition::getTouchAdPositionList()[$model->center_touch_ad_position_id],
                    'type' => DetailView::INPUT_SELECT2,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'widgetOptions' => [
                        'data' => \common\models\TouchAdPosition::getTouchAdPositionList(),
                        'options' => ['placeholder' => '选择微信中部广告位'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'main_cat',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'brand_area',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => \backend\models\Brand::$brand_area_map,
                        'options' => ['placeholder' => '选择所属区域'],
                        'pluginOptions' => ['width'=>'100%',],
                    ],
                ],
                [
                    'label' => '营业的所有品类',
                    'attribute' => 'brandCatIds',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'value' => function ($model) use($model)  {
                        $brand = \common\models\BrandCat::find()->joinWith([
                            'category'
                        ])->where([
                            'brand_id' => $model->brand_id,
                        ])->asArray()->all();
                        $result = '';
                        foreach ($brand as $brandStr) {
                            foreach ($brandStr['category'] as $categoryStr)
                                $result .= ''. $categoryStr['cat_name']. '|';
                        }
                        if (!empty($result)) {
                            return substr($result, 0, -1);
                        } else {
                            return '';
                        }
                    },
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => \common\models\Category::getTopCatMap(),
                        'options' => ['placeholder' => '选择经营品类'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%', 'multiple' => true,],
                    ],
                    
                ],
            ],
        ],
        [
            'group' => true,
            'label' => '品牌详情',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'brand_content',
                    'format' => 'raw',
                    'value' => empty($model->touchBrand) ? '' : $model->touchBrand->brand_content,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 90%',
                    ],
                    'viewModel' => $model->touchBrand,
                    'editModel' => $model->touchBrand,
                    'type' => DetailView::INPUT_WIDGET,
                    'widgetOptions' => [
                        'class' => 'kucha\ueditor\UEditor',
                    ],
                ],
            ],
        ],
        [
            'group' => true,
            'label' => '品牌授权',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'license',
                    'format' => 'raw',
                    'value' => empty($model->touchBrand) ? '' : $model->touchBrand->license,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 90%',
                    ],
                    'viewModel' => $model->touchBrand,
                    'editModel' => $model->touchBrand,
                    'type' => DetailView::INPUT_WIDGET,
                    'widgetOptions' => [
                        'class' => 'kucha\ueditor\UEditor',
                    ],
                ],
            ],
        ],
        [
            'group' => true,
            'label' => '品牌进口资质',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'brand_qualification',
                    'format' => 'raw',
                    'value' => empty($model->touchBrand) ? '' : $model->touchBrand->brand_qualification,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 90%',
                    ],
                    'viewModel' => $model->touchBrand,
                    'editModel' => $model->touchBrand,
                    'type' => DetailView::INPUT_WIDGET,
                    'widgetOptions' => [
                        'class' => 'kucha\ueditor\UEditor',
                    ],
                ],
            ],
        ],
    ];

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => DetailView::MODE_VIEW,
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->brand_id, 'delete' => true],
        ],
        'panel'=>[
            'heading'=>'品牌：' . $model->brand_name,
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'buttons1' => '{update}',
        'formOptions' => [
            'action' => \yii\helpers\Url::to(['update', 'id' => $model->brand_id]),
        ],
        'fadeDelay' => 100,
    ]);
    ?>
</div>

<?=
    $this->render('_createSpecGoodsCat', [
        'brandId' => $model->brand_id,
        'model' => $newSpecCat,
    ])
?>

<?php

    if (!empty($model['brandSpecCatList'])) {
        foreach ($model['brandSpecCatList'] as $specCat) {
            echo $this->render('_specGoodsList', [
                'specCat' => $specCat,
            ]);
        }
    }

?>
