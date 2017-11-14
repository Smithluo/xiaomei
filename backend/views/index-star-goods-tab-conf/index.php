<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexStarGoodsTabConfSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页楼层配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-goods-tab-conf-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建首页楼层配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'tab_name',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-goods-tab-conf/edit-name'],
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
                        'header' => '排序值',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-goods-tab-conf/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'm_url',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '更多链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-goods-tab-conf/edit-m-url'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'pc_url',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '更多链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-goods-tab-conf/edit-pc-url'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
