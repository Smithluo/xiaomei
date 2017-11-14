<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\KnowledgeShowBrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '美妆知识库推荐的品牌';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="knowledge-show-brand-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render(
            '_search',
            [
                'model' => $searchModel,
                'brandMap' => $brandMap,
                'platformMap' => $platformMap,
            ]
    ); ?>

    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '操作'
            ],

            'id',
            [
                'attribute' => 'brand_id',
                'value' => function ($model) use ($brandMap) {
                    return $brandMap[$model->brand_id];
                }
            ],
            'sort_order',
            [
                'attribute' => 'platform',
                'value' => function ($model) use ($platformMap) {
                    return isset($platformMap[$model->platform]) ? $platformMap[$model->platform] : '未设置';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
