<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsPkgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品包';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-pkg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建商品包', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'pkg_id',
            'pkg_name',
            'allow_goods_list:ntext',
            'deny_goods_list:ntext',
            'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update}',
            ],
        ],
    ]); ?>
</div>
