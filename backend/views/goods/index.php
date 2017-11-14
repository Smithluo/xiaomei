<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\dynagrid\DynaGrid;
use backend\models\Category;
use backend\models\UserRank;
use backend\models\Brand;
use backend\models\Goods;
use common\helper\DateTimeHelper;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品';
$this->params['breadcrumbs'][] = $this->title;
$isOrNotMap = Yii::$app->params['is_or_not_map'];
$userRankMap = UserRank::$user_rank_map;
?>
<div class="goods-index">
    tips: 商品图片、商品相册、关联商品、配件、关联文章  暂时在旧后台操作，其他功能都可以在新后台操作
    <span style="color: red">
        <strong>注意：</strong>新增的 【复制为积分商品】 复制后的商品是 下架状态的积分商品，一定要注意价格修改为不带小数的值
    </span>
    <p style="color: red"><strong>商品上架前一定要在商品编辑页面检查PC站、微信站的预览，在列表页不给直接上架的按钮。点击 商品ID 可以跳转到PC站的商品详情页(这里不是预览)</strong></p>
    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
        <?php
            if(!empty($ranks)) {
                foreach ($ranks as $rank) {
                    $url = str_replace(
                        '/goods/index',
                        '/goods/export',
                        urldecode(Yii::$app->request->url)
                    );

                    if (strstr($url, '?')) {
                        $url = $url. '&rank='.$rank->rank_id;
                    }
                    else {
                        $url = $url. '?rank='.$rank->rank_id;
                    }

                    echo Html::a('导出商品列表('. $rank->rank_name. ')',
                        $url,
                        ['class' => 'btn btn-default']);
                }
            }
        ?>
    </p>
    <div class="row">
        <div class="col-lg-10">
            <?= $this->render('_search', [
                'model' => $searchModel,
                'cat_id_map' => $cat_id_map,
            ]) ?>
        </div>
        <div class="col-lg-2">

            <p class="row">
                <?php

                ?>
                <?php
                    if (Yii::$app->user->can('/goods/update-goods-info')) {
                        $form = ActiveForm::begin([
                            'action' => ['update-goods-info'],
                            'method' => 'post',
                            'options' => ['enctype' => 'multipart/form-data'
                            ]]);
                        echo $form->field($goodsInfoImportModel, 'file')->fileInput();
                        echo '<button>提交</button>';

                        ActiveForm::end();
                    } else {
                        echo 'tips：原则上，导入数据的需求 只在数据逻辑变化，需要给某些属性指定初始值的时候 给予支持。没有逻辑改动的情况下，商品数据的变化，手动逐个修改。';
                    }
                ?>
            </p>

        </div>
    </div>
    <?php
    $columns = [
        [
            'attribute' => 'goods_id',
            'format' => 'raw',
            'value' => function($model){
                if ($model->extension_code == 'integral_exchange') {
                    $url = Yii::$app->params['pcHost'].'/exchange.php?act=info&id='.$model->goods_id;
                } else {
                    $url = Yii::$app->params['pcHost'].'/goods.php?id='.$model->goods_id;
                }
                return '<a target="_blank" href="'.$url.'">'.$model->goods_id.'</a>';
            }
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_name',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '商品名称',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editGoodsName'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'spu_id',
            'value' => function($model, $key, $index) use ($spuMap) {
                if (!empty($model->spu_id) && isset($spuMap[$model->spu_id])) {
                    return  $spuMap[$model->spu_id];
                } else {
                    return '未设置SPU';
                }
            },
            'editableOptions' => function($model, $key, $index) use ($spuMap) {
                return [
                    'header' => '条码前缀',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editSpuId'],

                    ],
//                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
//                    'data' => Goods::$prefixMap,
                    'inputType' => Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => $spuMap
                    ]
                ];
            },
            'pageSummary' => true,
            'format' => 'html',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sku_size',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '规格',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editSkuSize'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_sn',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '货号',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editGoodsSn'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'prefix',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '条码前缀',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editPrefix'],

                    ],
//                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
//                    'data' => Goods::$prefixMap,
                    'inputType' => Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => Goods::$prefixMap
                    ]
                ];
            },
            'pageSummary' => true,
            'format' => 'html',
            'value' => function($model){
                return isset(Goods::$prefixMap[$model->prefix]) ? Goods::$prefixMap[$model->prefix] : '未配置';
            }
        ],
        [
            'attribute' => 'is_on_sale',
            'format' => 'html',
            'value' => function($model){
                return Goods::$is_on_sale_icon_map[$model->is_on_sale];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'keywords',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '关键词,用英文“,”隔开',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editKeywords'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_brief',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editGoodsBrief'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editSort'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'is_hot',
            'pageSummary' => 'Total',
            'vAlign' => 'middle',
            'width' => '210px',
            'editableOptions' => function ($model, $key, $index) use ($isOrNotMap){
                return [
                    'header' => '选择所属省份',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_SWITCH,

                    'pluginOptions' => [
                        'onText' => '推荐',
                        'offText' => '不推荐',
                    ],

                    'data' => $isOrNotMap,

                    'formOptions' => [
                        'method' => 'post',
                        'action' => Url::to('/goods/editHot'),
                    ],
                ];
            },
            'format' => 'raw',

            'value' => function($model) use ($isOrNotMap){
                return $isOrNotMap[$model->is_hot];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_number',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editGoodsNumber'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'start_num',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editStartNum'],
                    ],
                ];
            },
            'format' => 'raw',
            'value' => function($model){
                //  按箱购买的商品校验起售数量是否是整箱数量
                if ($model->buy_by_box) {
                    if ($model->number_per_box) {
                        if ($model->start_num % $model->number_per_box != 0) {
                            return '<span style="color: orangered"><strong>'.$model->start_num.'</strong></span>';
                        }
                    } else {
                        return '<span style="color: red"><strong>'.$model->start_num.'</strong></span>';
                    }
                }
                return $model->start_num;
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'measure_unit',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editMeasureUnit'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'qty',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editNumberPerBox'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'number_per_box',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editNumberPerBox'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'buy_by_box',
            'editableOptions' => function($model, $key, $index) use ($isOrNotMap){
                return [
                    'size' => 'md',
                    'data' => $isOrNotMap,
                    'formOptions' => [
                        'action' => ['/goods/editBuyByBox'],
                    ],
                    'inputType' => Editable::INPUT_SWITCH,

                    'pluginOptions' => [
                        'onText' => '按箱',
                        'offText' => '不按箱',
                    ],
                ];
            },
            'format' => 'raw',
            'value' => function ($model) {
                $buyByBoxMap = Goods::$buyByBoxMap;
                return $buyByBoxMap[$model->buy_by_box];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_weight',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editGoodsWeight'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'sale_count',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'base_sale_count',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/edit-value'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'market_price',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editMarketPrice'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'shop_price',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editShopPrice'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'attribute' => 'discount_disable',
            'value' => function($model, $key, $index) {
                return empty($model->discount_disable) ? '参与会员折扣': '不参与会员折扣';
            },
        ],
        //  卖家备注
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'seller_note',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editSellerNote'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'shelf_life',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods/editShelfLife'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'min_price',
        //  当前只有积分商品启用了所需等级验证，普通商品的验证待定
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'need_rank',

            'editableOptions' => function ($model, $key, $index) use ($userRankMap){
                return [
                    'header' => '选择所需等级',
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,

                    'data' => $userRankMap,

                    'formOptions' => [
                        'method' => 'post',
                        'action' => Url::to('/goods/editNeedRank'),
                    ],
                ];
            },
            'value' => function($model) use ($userRankMap){
                return $userRankMap[$model->need_rank];
            },
            'pageSummary' => true,
        ],
        //  商品主图考虑做成鼠标经过时 悬浮显示，也可考虑悬浮显示商品参与的活动  等详情信息
        /*[
            'attribute' => 'goods_thumb',
            'format' => 'raw',
            'value' => function($model){
                return Html::img(ImageHelper::get_image_path($model->goods_thumb));
            }
        ],*/
        [
            'attribute' => 'cat_id',
            'value' => function($model){
                $cat = Category::findOne(['cat_id' => $model->cat_id]);
                return $cat['cat_name'];
            }
        ],
        [
            'attribute' => 'brand_id',
            'value' => function($model){
                $brand = Brand::findOne(['brand_id' => $model->brand_id]);
                return $brand['brand_name'];
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
        [
            'attribute' => 'is_delete',
            'format' => 'raw',
            'value' => function($model){
                return Goods::$is_delete_icon_map[$model->is_delete];
            },
        ],
        'click_count',
        'is_real',
        [
            'attribute' => 'add_time',
            'value' => function($model){
                return DateTimeHelper::getFormatCNDateTime($model->add_time);
            },
        ],
        [
            'label' => '标签',
            'format' => 'html',
            'value' => function($model) use($allTagIds){
                $tags_str = '';
                if (!empty($model->tags)) {
                    foreach ($model->tags as $tag) {
                        if (!$tags_str) {
                            $tags_str .= !empty($allTagIds[$tag->id]) ? $allTagIds[$tag->id] : '';
                        } else {
                            $tags_str .= !empty($allTagIds[$tag->id]) ? '<br />'.$allTagIds[$tag->id] : ' ';
                        }
                    }

                }
                $tags_str = trim($tags_str);
                $tags_str = trim($tags_str, '|');

                return $tags_str;
            },
        ],
//        [
//            'class' => 'kartik\grid\EditableColumn',
//            'attribute' => 'tagIds',
//            'editableOptions' => function ($model, $key, $index) use ($allTagIds){
//                return [
//                    'size' => 'md',
//                    'inputType' => Editable::INPUT_SELECT2  ,
//
//                    'data' => $allTagIds,
//
//                    'formOptions' => [
//                        'method' => 'post',
//                        'action' => Url::to('/goods/editTagIds'),
//                    ],
//                ];
//            },
//
//            'pageSummary' => true,
//        ],
        
        [
            'attribute' => 'supplier_user_id',
            'value' => function($model) {
                if (empty($model->supplierUser)) {
                    return '';
                }
                return $model->supplierUser->showName. '('. $model->supplierUser->company_name. ')';
            }
        ],
        [
            'attribute' => 'expire_date',
            'value' => function($model) {
                if (!$model->expire_date) {
                    return '未设置';
                } else {
                    return DateTimeHelper::getFormatCNDateTime($model->expire_date);
                }
            }
        ],
        'goodsSample',
        'goodsRegion',
        'goodsEffect',
//        [
//            'class' => 'kartik\grid\EditableColumn',
//            'attribute' => 'expire_date',
//
//
//            'editableOptions' => function ($model, $key, $index) {
//                return [
//                    'size' => 'md',
//                    'inputType' => Editable::INPUT_DATE,
//                    'convertFormat' => true,
//                    'pluginOptions' => [
//                        'singleDatePicker'=>true,
//                        'showDropdowns'=>true,
//                        'value' => $model->expire_date,
//                        'format' => 'yyyy-mm-dd',
//                        'todayHighlight' => true,
//                        'autoclose' => true,
//                    ],
//
//                    'formOptions' => [
//                        'method' => 'post',
//                        'action' => Url::to('/goods/editExpireDate'),
//                    ],
//                ];
//            },
//            'pageSummary' => true,
//        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{star} {view} {update} {copy} {integral} {delete} {toggle}',
            'buttons' => [
                'star' => function ($url, $model, $key) {
                    return
                        Html::a(
                            $model->tagStar
                                ? '<span class="glyphicon glyphicon-star"></span>'
                                : '<span class="glyphicon glyphicon-star-empty"></span>',
                            $url,
                            [
                                'title' => $model->tagStar ? '撤下标签' : '置为明星单品',
                            ]
                        );
                },
                'copy' => function ($url, $model, $key) {
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon glyphicon-adjust"></span>',
                            $url,
                            [
                                'title' => '复制',
                                'target' => '_blank'
                            ]
                        );
                },
                'integral' => function ($url, $model, $key) {
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-asterisk"></span>',
                            $url,
                            [
                                'title' => '复制为积分商品',
                                'target' => '_blank'
                            ]
                        );
                },
                'toggle' => function ($url, $model, $key) {
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-question-sign"></span>',
                            $url,
                            [
                                'title' => $model->is_on_sale ? '置为下架' : '置为上架',
                            ]
                        );
                },
            ],
        ],
    ];

    echo DynaGrid::widget([
//        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">商品列表</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                    ['index'],
                    ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-goods-index',
        ],
    ]);
    ?>

</div>
