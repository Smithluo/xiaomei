<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivitySortSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动显示顺序配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-sort-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <h2>排序值大的显示在前，排序值范围 0~65535</h2>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Activity Sort', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'alias',
            'link',
            'sort_order',
            [
                'attribute' => 'is_show',
                'value' => function ($model) use ($isShowMap){
                    return $isShowMap[$model->is_show];
                }
            ],
            // 'show_limit',    暂时未启用

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
