<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\helper\ImageHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkg */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="gift-pkg-form">
    <div>
        <P>操作说明</P>
        <P>礼包配置的商品可以是下架的，但不能是已删除的； 礼包的总价要比 不安礼包购买的商品总价 底，即优惠金额要>0， 否则用户下单会失败</P>
        <P>创建礼包的时候可以指定参与礼包的SKU，默认每个SKU的数量都是1，修改配置数量需要在礼包商品配置页修改，修改礼包SKU、数量后 要符合上一条要求</P>
        <P>礼包配置的商品要么全是同一个品牌的并且是品牌方发货的商品，要么全是小美直发的商品。</P>
    </div>

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
                    'attribute' => 'name',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'price',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'shipping_code',
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items' => $shippingList,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'is_on_sale',
                    'value' => $model->is_on_sale ? '上架' : '下架',
                    'type' => DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => '上架',
                            'offText' => '下架',
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'original_img',
                    'format' => 'raw',
                    'value' => Html::img($model->original_img, ['style' => 'width:200px; height:200px']),
                    'type' => DetailView::INPUT_FILE,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'brief',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'hint' => '用(,)隔开',
                ],
                [
                    'attribute' => 'updated_at',
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'updated_by',
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'desc',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'giftGoodsList',
                    'label' => $model->isNewRecord
                        ? '礼包活动的商品列表'
                        : '礼包活动的商品列表 '.Html::a('礼包SKU数量配置', '/gift-pkg-goods/index?GiftPkgGoodsSearch[gift_pkg_id]='.$model->id),
                    'format' => 'raw',
                    'value' => !empty($giftGoodsList) ? implode('<br />', $giftGoodsList) : '',
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => $goodsList,
                        'options' => [
                            'placeholder' => '选择参与改礼包的商品',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'width'=>'100%',
                        ],
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 60%',
                    ],
                ]
            ]
        ],

        [
            'group' => true,
            'label' => '活动详情',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'pkg_desc',
                    'format' => 'raw',
//                        'value' => TextHelper::formatRichText($model->goods_desc),
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

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'enableEditMode' => true,
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading' => '礼包活动',
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'formOptions' => [
            'action' => $model->isNewRecord
                ? \yii\helpers\Url::to(['create'])
                : \yii\helpers\Url::to(['update', 'id' => $model->id]),
        ],
    ]);
    ?>
</div>
