<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EventToGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event To Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-to-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Event To Goods', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'event_id',
            'goods_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
