<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BrandSpecGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Brand Spec Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-spec-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Brand Spec Goods', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'spec_goods_cat_id',
            'goods_id',
            'sort_order',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
