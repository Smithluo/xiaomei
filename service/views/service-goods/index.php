<?php

use yii\helpers\Html;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel service\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods';
$this->params['breadcrumbs'][] = $this->title;

\service\assets\GoodsAsset::register($this);

?>
<div class="goods-index">
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'goods_id',
            'cat_id',
            'goods_sn',
            'goods_name',
            'goods_name_style',
            // 'click_count',
            // 'brand_id',
            // 'provider_name',
            // 'goods_number',
            // 'measure_unit',
            // 'number_per_box',
            // 'goods_weight',
            // 'market_price',
            // 'shop_price',
            // 'min_price',
            // 'promote_price',
            // 'promote_start_date',
            // 'promote_end_date',
            // 'warn_number',
            // 'keywords',
            // 'goods_brief',
            // 'goods_desc:ntext',
            // 'goods_thumb',
            // 'goods_img',
            // 'original_img',
            // 'is_real',
            // 'extension_code',
            // 'is_on_sale',
            // 'is_alone_sale',
            // 'is_shipping',
            // 'integral',
            // 'add_time',
            // 'sort_order',
            // 'is_delete',
            // 'is_best',
            // 'is_new',
            // 'is_hot',
            // 'is_spec',
            // 'is_promote',
            // 'bonus_type_id',
            // 'last_update',
            // 'goods_type',
            // 'seller_note',
            // 'give_integral',
            // 'rank_integral',
            // 'suppliers_id',
            // 'is_check',
            // 'children',
            // 'shelf_life',
            // 'servicer_strategy_id',

            [
                'label' => '二级服务商分成比例',
                'value' => function($model) {
                    if($model->specServicerStrategy) {
                        return $model->specServicerStrategy->percent_level_2;
                    }
                    return 0;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
