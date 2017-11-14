<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel brand\models\BrandDivideRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Brand Divide Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-divide-record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Brand Divide Record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'brand_id',
            'goods_amount',
            'shipping_fee',
            // 'user_id',
            // 'divide_amount',
            // 'cash_record_id',
            // 'created_at',
            // 'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
