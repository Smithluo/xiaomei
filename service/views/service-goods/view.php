<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model service\models\Goods */

$this->title = $model->goods_id;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->goods_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->goods_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'goods_id',
            'cat_id',
            'goods_sn',
            'goods_name',
            'goods_name_style',
            'click_count',
            'brand_id',
            'provider_name',
            'goods_number',
            'measure_unit',
            'number_per_box',
            'goods_weight',
            'market_price',
            'shop_price',
            'min_price',
            'promote_price',
            'promote_start_date',
            'promote_end_date',
            'warn_number',
            'keywords',
            'goods_brief',
            'goods_desc:ntext',
            'goods_thumb',
            'goods_img',
            'original_img',
            'is_real',
            'extension_code',
            'is_on_sale',
            'is_alone_sale',
            'is_shipping',
            'integral',
            'add_time',
            'sort_order',
            'is_delete',
            'is_best',
            'is_new',
            'is_hot',
            'is_spec',
            'is_promote',
            'bonus_type_id',
            'last_update',
            'goods_type',
            'seller_note',
            'give_integral',
            'rank_integral',
            'suppliers_id',
            'is_check',
            'children',
            'shelf_life',
            'servicer_strategy_id',
        ],
    ]) ?>

</div>
