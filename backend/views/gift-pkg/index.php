<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GiftPkgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '礼包活动';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建礼包活动', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'desc',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '显示在首页的描述',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/gift-pkg/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'img',
            'price',
            [
                'attribute' => 'shipping_code',
                'value' => function ($model) use ($shippingList) {
                    return $shippingList[$model->shipping_code];
                }
            ],
            'brief',
            [
                'attribute' => 'is_on_sale',
                'value' => function ($model) use ($isOnSaleMap) {
                    return $isOnSaleMap[$model->is_on_sale];
                }
            ],
            'updated_at',
            'updated_by',
//             'pkg_desc:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {toggle}',
                'buttons' => [
                    'toggle' => function ($url, $model, $key) {
                        return
                            Html::a(
                                $model->is_on_sale
                                    ? '<span class="glyphicon glyphicon-remove"></span>'
                                    : '<span class="glyphicon glyphicon-ok"></span>',
                                $url,
                                [
                                    'title' => $model->is_on_sale ? '下架' : '上架',
                                ]
                            );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
