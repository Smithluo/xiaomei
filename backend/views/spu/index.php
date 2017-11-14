<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SpuSeach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SPU';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spu-index">

    <h1>SPU设置 建议 直发与非直发的商品 不使用同一个SPU记录</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建SPU', ['create'], ['class' => 'btn btn-success']) ?>
        <?=Html::a('导出SPU列表', '/spu/export',['class' => 'btn btn-default']);?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
