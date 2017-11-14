<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NewArrivedGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'New Arrived Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-arrived-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create New Arrived Goods', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    return $model['goods']['goods_name'];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '品牌',
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \backend\models\Goods::getGoodsMap(),
                        ],
                        'formOptions' => [
                            'action' => ['/new-arrived-goods/edit-value'],
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
                            'action' => ['/new-arrived-goods/edit-value'],
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
