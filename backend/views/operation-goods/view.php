<?php

use backend\models\Goods;
use backend\models\UserRank;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = $model->goods_name;
$this->params['breadcrumbs'][] = ['label' => $model->goods_name, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$is_or_not_map = Yii::$app->params['is_or_not_map'];
?>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->goods_id], ['class' => 'btn btn-primary']) ?>
         | tips:需要在这里查看商品的其他信息，可以提需求
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'tagIds',
                'value' => $tags_str
            ],
            'goods_id',
            'goods_sn',
            'goods_name',
            [
                'attribute' => 'is_best',
                'value' => $is_or_not_map[$model->is_best]
            ],
            [
                'attribute' => 'is_new',
                'value' => $is_or_not_map[$model->is_new]
            ],
            [
                'attribute' => 'is_hot',
                'value' => $is_or_not_map[$model->is_hot]
            ],
            [
                'attribute' => 'is_spec',
                'value' => $is_or_not_map[$model->is_spec]
            ],
            'sort_order',
            [
                'attribute' => 'extension_code',
                'format' => 'html',
                'value' => Goods::$extensionCodeMap[$model->extension_code],
            ],
            [
                'attribute' => 'need_rank',
                'value' => UserRank::$user_rank_map[$model->need_rank],
            ],
        ],
    ]) ?>

</div>
