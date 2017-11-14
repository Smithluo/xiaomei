<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Goods;
use kartik\grid\GridView;
use backend\models\Category;
use backend\models\Brand;
use backend\models\UserRank;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OperationGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '运营商品页面';
$this->params['breadcrumbs'][] = $this->title;
$is_on_sale_map = Goods::$is_on_sale_map;
$is_or_not_map = Yii::$app->params['is_or_not_map'];
$user_rank_map = UserRank::$user_rank_map
?>
<div class="goods-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php
        $ranks = \common\models\UserRank::find()->all();
        if(!empty($ranks)) {
            foreach ($ranks as $rank) {
                echo Html::a('导出商品列表('. $rank->rank_name. ')', ['/goods/export', 'rank' => $rank->rank_id], ['class' => 'btn btn-default']);
            }
        }
        ?>
    <span>tips:购物车推荐商品当前显示10个，至少保证有15款上架商品设置为推荐，确保有商品下架的时候不出现空白</span>
    </p>

    <div >
        <?= $this->render('_search', [
            'model' => $searchModel,
            'cat_id_map' => $cat_id_map,
        ]) ?>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'goods_id',
                'value' => function($model) {
                    return $model->goods_id;
                },
                'options' => [
                    'style' => 'width:50px',
                ],
            ],
            [
                'attribute' => 'goods_name',
                'format' => 'raw',
                'value' => function($model){
                    $url = Yii::$app->params['pcHost'].'/goods.php?id='.$model->goods_id;
                    return '<a target="_blank" href="'.$url.'">'.$model->goods_name.'</a>';
                },
            ],
            [
                'attribute' => 'goods_sn',
                'value' => function($model) {

                    return $model->goods_sn;
                },
                'options' => [
                    'style' => 'width:100px',
                ],
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'keywords',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '关键词,用英文“,”隔开',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-keywords'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'is_on_sale',
                'value' => function($model) use ($is_on_sale_map){
                    return $is_on_sale_map[$model->is_on_sale];
                },
            ],
            [
                'attribute' => 'cat_id',
                'value' => function($model){
                    $cat = Category::findOne(['cat_id' => $model->brand_id]);
                    return $cat['cat_name'];
                }
            ],
            [
                'attribute' => 'brand_id',
                'value' => function($model){
                    $brand = Brand::findOne(['brand_id' => $model->brand_id]);
                    return $brand['brand_name'];
                }
            ],
//            'goods_brief',
//            'goods_name_style',
             'click_count',
            // 'provider_name',
             'goods_number',
             'measure_unit',
             'number_per_box',
            // 'goods_weight',
            // 'market_price',
             'shop_price',
            // 'min_price',
            // 'promote_price',
            // 'promote_start_date',
            // 'promote_end_date',
            // 'warn_number',

            // 'goods_desc:ntext',
            // 'goods_thumb',
            // 'goods_img',
            // 'original_img',
            // 'is_real',
            // 'extension_code',
            // 'is_alone_sale',
            // 'is_shipping',
            // 'integral',
            // 'add_time',
            // 'is_delete',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_hot',
                'value' => function($model) use ($is_or_not_map){
                    return $is_or_not_map[$model->is_hot];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '购物车推荐',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-hot'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            /*[
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_best',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否精品',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-best'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_new',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否新品',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-new'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_spec',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否特供',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-goods/edit-spec'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],*/
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
            // 'start_num',
            // 'certificate',
            // 'discount_disable',
            // 'shipping_code',
            // 'complex_order',
            [
                'attribute' => 'need_rank',
                'value' => function($model) use ($user_rank_map){
                    return $user_rank_map[$model->need_rank];
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{update} | {view}',  //  {copy} |
                'buttons' => [
                    'copy' => function ($url, $model, $key) {
                        return
                            Html::a(
                                '<span class="glyphicon glyphicon-adjust"></span>',
                                $url,
                                [
                                    'title' => '复制一款商品用作活动，先的商品为下架状态',
                                    'target' => '_blank'
                                ]
                            );
                    },

                ],
                'options' => [
                    'style' => 'width:150px',
                ],
            ],
        ],
    ]); ?>
</div>



