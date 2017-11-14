<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GoodsActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Actions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'user_name',
            'goods_id',
            'goods_name',
            'shop_price',
            [
                'attribute' => 'disable_discount',
                'value' => function ($model) {
                    if ($model->disable_discount == 1) {
                        return '1(不参与)';
                    }
                    elseif ($model->disable_discount == 0) {
                        return '0(参与)';
                    }
                    else {
                        return '-1(未变更)';
                    }
                }
            ],
            'goods_number',
            'volume_price:ntext',
            'time',
        ],
    ]); ?>
</div>
