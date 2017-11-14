<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Goods */

$this->title = $model->goods_name;
$this->params['breadcrumbs'][] = ['label' => '商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'goods_id',
            'goods_number',
            'warn_number',
            'cat_id',
            'goods_sn',
            'start_num',
//            'goods_name',
//            'goods_name_style',
            'click_count',
//            [
//                'attribute'=>'brand_id',
//                'format' => 'html',
//                'value'=> $model->brand->brand_name,
//            ],
//            'provider_name',
            'measure_unit',
            'number_per_box',
//            'goods_weight',
            'market_price',
            'shop_price',
            'min_price',
//            'promote_price',
//            'promote_start_date',
//            'promote_end_date',

//            'keywords',
//            'goods_brief',
//            'goods_desc:ntext',
//            'goods_thumb',
//            'goods_img',
//            'original_img',
//            'is_real',
//            'extension_code',
//            'is_on_sale',
//            'is_alone_sale',
//            'is_shipping',
//            'integral',
//            'add_time',
//            'sort_order',
//            'is_delete',
//            'is_best',
//            'is_new',
//            'is_hot',
//            'is_spec',
//            'is_promote',
//            'bonus_type_id',
//            'last_update',
//            'goods_type',
//            'seller_note',
//            'give_integral',
//            'rank_integral',
//            'suppliers_id',
//            'is_check',
//            'children',
//            'shelf_life',
        ],
    ]) ?>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->goods_id], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
