<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<p>↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓专辑商品列表↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓</p>

<?php

$items = $model->itemList;
$data = [];
foreach ($items as $item) {
    $data[$item['goods_id']] = '【'. $item['goods']['goods_id']. '】'. $item['goods']['goods_name']. '【'. $item['goods']['goods_sn']. '】';
}

echo GridView::widget([
    'dataProvider' => $itemDataProvider,
    'filterModel' => $itemSearchModel,
    'columns' => [
        [
            'attribute' => 'goods_id',
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'data' => $data,
                'options' => ['placeholder' => '商品'],
                'pluginOptions' => ['allowClear'=>true, 'width'=>'300px'],
            ],
        ],
        [
            'label' => '商品缩略图',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model['goods'])) {
                    return null;
                }
                return Html::img(\common\helper\ImageHelper::get_image_path($model['goods']['goods_thumb']), [
                    'style' => 'width: 50px; height: 50px',
                ]);
            }
        ],
        [
            'label' => '货号',
            'value' => function ($model) {
                if (empty($model['goods'])) {
                    return null;
                }
                return $model['goods']['goods_sn'];
            }
        ],
        [
            'label' => '商品名',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model['goods'])) {
                    return null;
                }
                return Html::a($model['goods']['goods_name'], Url::to(['/goods/view', 'id' => $model['goods_id']]), [
                    'target' => '_blank',
                ]);
            }
        ],
        [
            'label' => '价格',
            'value' => function ($model) {
                if (empty($model['goods'])) {
                    return null;
                }
                return $model['goods']['shop_price'];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods-collection/edit-item-sort'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return
                        \yii\helpers\Html::a(
                            '<span class="glyphicon glyphicon glyphicon-trash"></span>',
                            \yii\helpers\Url::to(['/goods-collection/delete-item', 'id' => $model->id]),
                            [
                                'title' => '删除',
                                'data-method' => 'post',
                                'data-confirm' => '确定删除此项吗？',
                            ]
                        );
                },
            ],
        ],
    ],
]);
?>


