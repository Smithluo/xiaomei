<?php

use yii\helpers\Html;
use backend\models\Goods;
use kartik\detail\DetailView;
use backend\models\Brand;
use backend\models\Category;
use backend\models\Users;
use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\TextHelper;
use backend\models\Shipping;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */
//  商品品牌列表
$brandList = Brand::getBrandListMap();

//  商品扩展分类
$goodsCatColumns = [];
$goodsCatModel = $model->getGoodsCat()->asArray()->all();
$goodsCatList = array_column($goodsCatModel, 'cat_id');
$goodsCatNameList = Category::getCatList($goodsCatList, 'cat_name');
$model->goodsCatList = $goodsCatList;
//$extCatList = Category::getGoodsCatIdMap(299);  //  获取商品分类的分类树
$extCatLeaves = \common\helper\CacheHelper::getAllCategoryLeaves();
$extCatList = [];
foreach ($extCatLeaves as $catItem) {
    $topCatName = $catItem['cat_name'];
    if (!empty($catItem['leaves'])) {
        foreach ($catItem['leaves'] as $leaf) {
            $extCatList[$leaf['cat_id']] = '('. $leaf['cat_id']. ')'. $topCatName. '-'. $leaf['cat_name'];
        }
    }
}

$goodsCatColumns['columns'] = [
    [
        'attribute' => 'goodsCatList',
        'format' => 'raw',
        'value' => implode('<br />', $goodsCatNameList),
        'label' => '扩展分类',
        'labelColOptions' => [
            'style' => 'width: 10%',
        ],
        'valueColOptions' => [
            'style' => 'width: 90%',
        ],
        'type' => DetailView::INPUT_SELECT2,
        'showToggleAll' => false,
        'widgetOptions' => [
            'data' => $extCatList,
            'options' => [
                'placeholder' => '选择关联商品',
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear'=>true,
                'width'=>'100%',
            ],
        ],
    ],
];
$categoryName = '';
if (!empty($model->cat_id)) {
    $curCat = Category::findOne($model->cat_id);
    if (!empty($curCat)) {
        $categoryName = $curCat->cat_name;
    }
}

//  商品关联商品 仿照 扩展分类做
$linkGoodsColumns = [];
/*
$linkGoodsModel = $model->getLinkGoods()->asArray()->all();
$linkList = array_column($linkGoodsModel, 'link_goods_id');
$linkGoodsName = Goods::getGoodsName($linkList);
$goodsList = Goods::getUnDeleteGoodsMap();
$model->linkGoodsList = $linkList;
$linkGoodsColumns['columns'][] = [
    'attribute' => 'linkGoodsList',
    'format' => 'raw',
    'value' => implode('<br />', $linkGoodsName),
    'label' => '新增关联商品(这里显示所有商品，包含已下架)',
    'labelColOptions' => [
        'style' => 'width: 10%',
    ],
    'valueColOptions' => [
        'style' => 'width: 90%',
    ],
    'type' => DetailView::INPUT_SELECT2,
    'showToggleAll' => false,
    'widgetOptions' => [
        'data' => $goodsList,
        'options' => [
            'placeholder' => '选择关联商品',
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear'=>true,
            'width'=>'100%',
        ],
    ],
];
*/

//  商品所属品牌
$brand_name = '';
if ($model->brand) {
    $brand_name = $model->brand['brand_name'];
}

//  商品属性
$goodsAttr = [];
$goods_attr_id = Yii::$app->params['goods_attr_id'];
$goods_attr_alias = array_flip($goods_attr_id);
if (!empty($model->goodsAttr)) {
    foreach ($model->goodsAttr as $attr) {
        if (isset($goods_attr_alias[$attr['attr_id']])) {
            $key = $goods_attr_alias[$attr['attr_id']];
            $goodsAttr[$key] = $attr['attr_value'];
        }
    }
}
//  商品分成比例（对服务商）
$percentTotal = 0;
if (!empty($model->percentTotal)) {
    $percentTotal = $model->percentTotal['percent_total'];
}
//  会员等级
$user_rank_map = Users::$user_rank_map;
$need_user_rank_map = Users::$need_user_rank_map;
//  是否项目
$is_or_not_map = Yii::$app->params['is_or_not_map'];
//  商品图片
if ($model->original_img) {
    $goodsOriginalImg = ImageHelper::get_image_path($model->original_img);
} else {
    $goodsOriginalImg = '未上传';
}

//  商品相册
if ($model->goods_id) {
    $creatGalleryUrl = '/goods-gallery/create?goods_id='.$model->goods_id;
} else {
    $creatGalleryUrl = '/goods-gallery/create';
}

$GoodsGalleryListColumns = [];
if ($GoodsGalleryList) {
    foreach ($GoodsGalleryList as $gallery) {
        $imgOriginal = ImageHelper::get_image_path($gallery->img_original);
        $updateGalleryUrl = '/goods-gallery/update?id='.$gallery->img_id;
        $GoodsGalleryListColumns[]['columns'] = [
            [
                'attribute' => '['.$gallery->img_id.']img_original',
                'label' => '原有轮播图',
                'format' => 'raw',
                'value' => !empty($gallery->img_original)
                    ? Html::img($imgOriginal, ['style' => 'width:200px; height:200px'])
                    : '请上传',
                'type' => DetailView::INPUT_FILE,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 15%',
                ],
                'viewModel' => $gallery,
                'editModel' => $gallery,
            ],
            [
                'attribute' => '['.$gallery->img_id.']img_desc',
                'label' => '轮播图描述',
                'format' => 'raw',
                'value' => $gallery->img_desc,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 15%',
                ],
                'viewModel' => $gallery,
                'editModel' => $gallery,
            ],
            [
                'attribute' => 'img_url',
                'label' => '图片路径',
                'format' => 'raw',
                'value' =>
                    '原　图：'.(!empty($gallery->img_original) ? $imgOriginal : '未上传').'<br /><br />'.
                    '缩略图：'.ImageHelper::get_image_path($gallery->thumb_url).'<br /><br />'.
                    '正常图：'.ImageHelper::get_image_path($gallery->img_url),
                'displayOnly' => true,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 40%',
                ],
                'viewModel' => $gallery,
                'editModel' => $gallery,
            ],
            [
                'label' => '删除',
                'format' => 'raw',
                'value' => function () use ($gallery, $model) {
                    return Html::a('删除', \yii\helpers\Url::to(['goods/delete-gallery', 'id' => $gallery['img_id'], 'goods_id' => $model['goods_id']]));
                },
                'displayOnly' => true,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'viewModel' => $gallery,
                'editModel' => $gallery,
            ],
        ];
    }
}

foreach ($moreGalleryList as $k => $moreGallery) {
    $GoodsGalleryListColumns[]['columns'] = [
        [
            'attribute' => '['.$k.']img_original',
            'label' => '新增轮播图'. $k,
            'value' => '待上传',
            'type' => DetailView::INPUT_FILE,
            'labelColOptions' => [
                'style' => 'width: 10%',
            ],
            'valueColOptions' => [
                'style' => 'width: 15%',
            ],
            'viewModel' => $moreGallery,
            'editModel' => $moreGallery,
        ],
        [
            'attribute' => '['.$k.']img_desc',
            'label' => '轮播图描述',
            'format' => 'raw',
            'value' => $moreGallery->img_desc,
            'labelColOptions' => [
                'style' => 'width: 10%',
            ],
            'valueColOptions' => [
                'style' => 'width: 15%',
            ],
            'viewModel' => $moreGallery,
            'editModel' => $moreGallery,
        ],
        [
            'attribute' => 'img_url',
            'label' => '图片路径',
            'value' => '待上传',
            'displayOnly' => true,
            'labelColOptions' => [
                'style' => 'width: 10%',
            ],
            'valueColOptions' => [
                'style' => 'width: 40%',
            ],
            'viewModel' => $moreGallery,
            'editModel' => $moreGallery,
        ]
    ];
}


//  判定当前用户是否有权修改
if (Yii::$app->user->can('/goods/update')) {
    $catUpdate = true;
} else {
    $catUpdate = false;
}

/*
//  运费id=>desc 映射
$shippingIdDescMap = Shipping::getShippingIdDescMap();

//  商品的实际配送方式
if ($model->shipping_id > 0) {
    $shippingName = $shippingIdDescMap[$model->shipping_id];
} elseif ($model->brand['shipping_id']) {
    $shippingName = $shippingIdDescMap[$model->brand['shipping_id']];
} else {
    $shippingName = '未设置将默认使用品牌的配送方式';    //  $shippingIdDescMap[$model->shipping_id]
}
*/

//  设置默认值
if ($model->isNewRecord) {
    $defaultShippingId = Shipping::getDefaultShippingId();
    $defaultValue = [
        'sort_order' => 30000,
        'measure_unit' => '件',
        'shipping_id' => 0,
        'supplier_user_id' => 0,
        'extension_code' => 'general',
        'need_rank' => 1,
        'is_delete' => 0,
        'is_real' => 1,
        'is_spec' => 0,
        'is_best' => 0,
        'is_new' => 0,
        'is_hot' => 0,
        'goods_weight' => 0.00,
    ];
    if ($model->isNewRecord) {
        foreach ($defaultValue as $key => $value) {
            if (empty($model->$key)) {
                $model->setAttribute($key, $value);
            }
        }
    }
}

?>

<p style="color: red">
    <strong>
        注意：品牌有设置服务商分成时，商品可不设置服务商分成，结算时商品的分成比例按品牌设置的分成比例 去计算
    </strong>
</p>
<p style="color: red">tips: 商品没有设置配送方式，系统会选择商品所属品牌的配送方式。 所以请确保品牌一定要有默认配送方式。商品分类没有默认计件单位，需要对每个商品设置计件单位</p>
<p style="color: red">
    <strong>
        注意：由小美发货的商品（小美直发、积分商品、团采商品）可能会设置所属品牌为小美诚品，那么没有设置配送方式的商品将会显示小美诚品这个品牌的默认配送方式
    </strong>
</p>

<?php
    if (!$model->isNewRecord) {
        echo Html::a('PC站预览', 'http://www.xiaomei360.com/goods_preview.php?id='. $model->goods_id, [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);

        echo Html::a('微信站预览', 'http://m.xiaomei360.com/default/goods/preview/id/'. $model->goods_id.'.html', [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);
    }
?>

<div class="goods-form">
    <?php
        $attributes = [
            [
                'group' => true,
                'label' => '基本信息',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'goods_name',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 40%',
                        ],
                    ],
                    [
                        'attribute' => 'goods_sn',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'certificate',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'shelf_life',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'is_on_sale',
                        'format' => 'raw',
                        'value' => $model->is_on_sale ?
                            '<span class="glyphicon glyphicon-ok"></span>'
                            : '<span class="glyphicon glyphicon-remove"></span>',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '上架',
                                'offText' => '不上架',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'sku_size',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'spu_id',
                        'value' => empty($spuMap[$model->spu_id]) ? '' : $spuMap[$model->spu_id],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 25%',
                        ],
                        'type' => DetailView::INPUT_SELECT2,
                        'widgetOptions' => [
                            'data' => $spuMap,
                            'options' => ['placeholder' => '选择所属SPU'],
                            'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                        ],
                    ],
                    [
                        'attribute' => 'keywords',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'cat_id',
                        'value' => $categoryName,
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'type' => DetailView::INPUT_SELECT2,
//                        'items' => $extCatList,
                        'widgetOptions' => [
                            'data' => $extCatList,
                            'options' => ['placeholder' => '选择所属分类'],
                            'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                        ],
                    ],
                    [
                        'attribute' => 'expire_date',
                        'format' => 'raw',
                        'type' => DetailView::INPUT_DATE,
                        'options' => ['placeholder' => date('Y-m-d', time())],
                        'convertFormat' => true,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'singleDatePicker'=>true,
                                'showDropdowns'=>true,
                                'value' => $model->expire_date,
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true,
                                'autoclose' => true,
                            ],
                        ],

                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],

                    ],
                    [
                        'attribute' => 'buy_by_box',
                        'format' => 'raw',
                        'value' => $model->buy_by_box ?
                            '<span class="glyphicon glyphicon-ok"></span>'
                            : '<span class="glyphicon glyphicon-remove"></span>',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '按箱购买',
                                'offText' => '不按箱购买',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'brand_id',
                        'value' => $brand_name,
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'type' => DetailView::INPUT_SELECT2,
                        'widgetOptions' => [
                            'data' => $brandList,
                            'options' => ['placeholder' => '选择所属品牌'],
                            'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                        ],
                    ],
                    [
                        'attribute' => 'goods_brief',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 25%',
                        ],
                    ],
                    [
                        'attribute' => 'extension_code',
                        'displayOnly' => true,
                        'value' => Goods::$extensionCodeMap[$model->extension_code],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'prefix',
                        'value' => isset(Goods::$prefixMap[$model->prefix]) ? Goods::$prefixMap[$model->prefix] : '未配置',
                        'type' => DetailView::INPUT_DROPDOWN_LIST,
                        'items' => Goods::$prefixMap,
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'add_time',
                        'displayOnly' => true,
                        'value' => DateTimeHelper::getFormatCNDateTime($model->add_time),
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'is_hot',
                        'format' => 'raw',
                        'value' => $model->is_hot ?
                            '<span class="glyphicon glyphicon-ok"></span>'
                            : '<span class="glyphicon glyphicon-remove"></span>',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '推荐',
                                'offText' => '不推荐',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],

                    ],
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'seller_note',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'integral',
                        'displayOnly' => true,
                        'label' => '赠送积分比例',
                        'value' => '实际支付金额 ÷ 10  向下取整',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'need_rank',
                        'value' => $user_rank_map[$model->need_rank],
                        'type' => DetailView::INPUT_DROPDOWN_LIST,
                        'items' => $need_user_rank_map,
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'servicerStrategy',
                        'value' => $percentTotal,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],

                    [
                        'attribute' => 'last_update',
                        'displayOnly' => true,
                        'value' => DateTimeHelper::getFormatCNDateTime($model->last_update),
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'is_real',
                        'format' => 'raw',
                        'value' => $model->is_real ?
                            '<span class="glyphicon glyphicon-ok"></span>'
                            : '<span class="glyphicon glyphicon-remove"></span>',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '真实商品',
                                'offText' => '虚拟商品',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                ]
            ],

            [
                'columns' => [
                    [
                        'attribute' => 'supplier_user_id',
                        'value' => $model->supplier_user_id ? $suppliers[$model->supplier_user_id] : '',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'type' => DetailView::INPUT_SELECT2,
                        'widgetOptions' => [
                            'data' => $suppliers,
                            'options' => ['placeholder' => '选择供应商'],
                            'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                        ],
                    ],
                    [
                        'attribute' => 'sort_order',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'complex_order',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'click_count',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'is_delete',
                        'label' => '逻辑删除',
                        'value' => $model->is_delete
                            ? '已删除'
                            : '未删除',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '已删除',
                                'offText' => '未删除',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                ]
            ],
            $goodsCatColumns,
//            $linkGoodsColumns,    //  关联商品暂时撤掉 使用spu关联商品

            [
                'group' => true,
                'label' => '价格信息',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'market_price',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'discount_disable',
                        'format' => 'raw',
                        'value' => $model->discount_disable
                            ? '不使用'
                            : '使用',
                        'type' => DetailView::INPUT_SWITCH,
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'onText' => '不使用',
                                'offText' => '使用',
                            ]
                        ],
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'start_num',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'number_per_box',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'measure_unit',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'moq_vip',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'shop_price',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'min_price',
                        'displayOnly' => true,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'goods_number',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'qty',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'goods_weight',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'moq_svip',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                ],
            ],

            [
                'group' => true,
                'label' => '商品属性',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'tagIds',
                        'format' => 'raw',
                        'value' => $tagsMap ? implode(' | ', $tagsMap) : '',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 25%',
                        ],
                        'type' => DetailView::INPUT_SELECT2,
                        'showToggleAll' => false,
                        'widgetOptions' => [
                            'data' => $allTagIds,
                            'options' => [
                                'placeholder' => '选择商品标签',
                                'multiple' => true
                            ],
                            'pluginOptions' => [
                                'allowClear'=>true,
                                'width'=>'100%',
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'region',
                        'value' => isset($goodsAttr['region']) ? $goodsAttr['region'] : '',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                    ],
                    [
                        'attribute' => 'effect',
                        'value' => isset($goodsAttr['effect']) ? $goodsAttr['effect'] : '',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'sample',
                        'label' => '物料配比(失效，参照满赠活动配置物料)',
                        'value' => isset($goodsAttr['sample']) ? $goodsAttr['sample'] : '',
                        'labelColOptions' => [
                            'style' => 'width: 15%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                ],
            ],

            [
                'group' => true,
                'label' => '商品首图',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'original_img',
                        'format' => 'raw',
                        'value' => Html::img($goodsOriginalImg, ['style' => 'width:200px; height:200px']),
                        'type' => DetailView::INPUT_FILE,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'goods_thumb',
                        'label' => '图片路径',
                        'format' => 'raw',
                        'displayOnly' => true,
                        'value' =>
                            '原　图：'.$goodsOriginalImg.'<br /><br />'.
                            '正常图：'.ImageHelper::get_image_path($model->goods_img).'<br /><br />'.
                            '缩略图：'.ImageHelper::get_image_path($model->goods_thumb).'<br />',
                        'labelColOptions' => [
                            'style' => 'width: 10%; height:33%;',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 65%; height:33%;',
                        ],
                    ],
                ],
            ],

            [
                'group' => true,
                'label' => '商品轮播图',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
/*
            [
                'columns' => [
                    [
                        'attribute' => 'volume_number_2',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'volume_price_2',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],

                    [
                        'attribute' => 'volume_number_1',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'volume_price_1',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                    [
                        'attribute' => 'shipping_id',
                        'value' => $shippingName,
                        'type' => DetailView::INPUT_DROPDOWN_LIST,
                        'items' => $shippingIdDescMap,
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 20%',
                        ],
                    ],

                ]
            ],
            */
        ];

        $attributes = \yii\helpers\ArrayHelper::merge($attributes, $GoodsGalleryListColumns);

        $goodsDesc = [
            [
                'group' => true,
                'label' => '商品详情',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'goods_desc',
                        'format' => 'raw',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 1000px',
                        ],
                        'type' => DetailView::INPUT_WIDGET,
                        'widgetOptions' => [
                            'class' => 'kucha\ueditor\UEditor',
                            'clientOptions' => [
                                'initialFrameHeight' => '600',
                                'autoHeightEnabled' => true,
                                'topOffset' => 50
                            ],
                        ],
                    ],

                ]
            ],
        ];

        $attributes = \yii\helpers\ArrayHelper::merge($attributes, $goodsDesc);

        $attributes = \yii\helpers\ArrayHelper::merge($attributes, [
            [
                'columns' => [
                    [
                        'attribute' => 'extension_code',
                        'type' => DetailView::INPUT_HIDDEN,
                        'value' => $model->extension_code,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                    ],
                ]
            ],
        ]);

        echo DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
            'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'enableEditMode' => true,
            'deleteOptions'=>[ // your ajax delete parameters
                'params' => ['id' => $model->goods_id, 'custom_param' => true],
            ],
            'panel'=>[
                'heading'=>'商品详情：' . $model->goods_name,
                'type'=>DetailView::TYPE_PRIMARY,
            ],
            'formOptions' => [
                'action' => $model->isNewRecord
                    ? \yii\helpers\Url::to(['create'])
                    : \yii\helpers\Url::to(['update', 'id' => $model->goods_id]),
            ],

//            'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
//            'buttons1' => '{update}',
            //  Yii::$app->controller->action->id == 'update' ? '{update}' : '',
        ]);
    ?>
</div>