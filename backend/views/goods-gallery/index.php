<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsGallerySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Galleries';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-gallery-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Goods Gallery', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'img_id',
            'goods_id',
            'img_url:url',
            'img_desc',
            'thumb_url:url',
            // 'img_original',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
