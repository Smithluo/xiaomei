<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ZhifaGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zhifa Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhifa-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Zhifa Goods', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'type',
                'value' => function ($model) {
                    return \common\models\ZhifaGoods::$typeMap[$model['type']];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '类型',
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \common\models\ZhifaGoods::$typeMap,
                        ],
                        'formOptions' => [
                            'action' => ['/zhifa-goods/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ZhifaGoods::$typeMap,
                    'options' => ['placeholder' => '类型'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    return $model['goods']['goods_name'];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '商品',
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \backend\models\Goods::getGoodsMap(),
                        ],
                        'formOptions' => [
                            'action' => ['/zhifa-goods/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \backend\models\Goods::getGoodsMap(),
                    'options' => ['placeholder' => '商品'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/zhifa-goods/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
                'filter' => false,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
