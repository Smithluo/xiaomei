<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use backend\models\GoodsActivity;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsActivity */

$this->title = $model->act_name;
$this->params['breadcrumbs'][] = ['label' => $act_type_map[$model->act_type], 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="goods-activity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->act_id], ['class' => 'btn btn-primary']) ?>
        <?php
            /*echo Html::a('Delete', ['delete', 'id' => $model->act_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);*/
        ?>
    </p>

    <div class="col-lg-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'act_name',
                'goods_id',
                'goods_name',
                'old_price',
                'act_price',
                'match_num',
                'sort_order',
                'number_per_box',
                [
                    'attribute' => 'buy_by_box',
                    'format' => 'raw',
                    'value' => \backend\models\Goods::$buyByBoxMap[$model->buy_by_box],
                ],
                [
                    'attribute' => 'price_ladder',
                    'format' => 'raw',
                    'value' => $model->price_ladder
                ],
                [
                    'attribute' => 'start_time',
                    'value' => DateTimeHelper::getFormatCNDateTime($model->start_time),
                ],
                [
                    'attribute' => 'end_time',
                    'value' => DateTimeHelper::getFormatCNDateTime($model->end_time),
                ],
                [
                    'attribute' => 'shipping_code',
                    'value' => $shippingCodeNameMap[$model->shipping_code] ?: '运费到付',
                ],
                [
                    'attribute' => 'order_expired_time',
                    'label' => '订单有效期',
                    'value' => DateTimeHelper::getTimeDesc($model->order_expired_time),
                ],
            ],
        ]) ?>
    </div>
    <div class="col-lg-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'act_desc',
                'sample',
                'old_price',
                'start_num',
                'limit_num',
                'deposit',
                'gift_integral',
                'production_date',
                [
                    'attribute' => 'is_hot',
                    'value' => GoodsActivity::$is_hot_map[$model->is_hot]
                ],
                [
                    'attribute' => 'is_finished',
                    'value' => GoodsActivity::$is_finished_map[$model->is_finished]
                ],
                [
                    'attribute' => 'ext_info',
                    'value' => json_encode(unserialize($model->ext_info))
                ]
            ],
        ]) ?>
    </div>
    <div class="col-lg-12">
        <div class="col-lg-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'show_banner',
                        'format' => 'html',
                        'value' => Html::img(
                            $model->getUploadUrl('show_banner'),
                            ['height' => '200']
                        ) ?: '图片未上传'
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'qr_code',
                        'format' => 'html',
                        'value' => Html::img(
                            $model->getUploadUrl('qr_code'),
                            ['height' => '200']
                        ) ?: '图片未上传'
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'goods_list',
                        'format' => 'html',
                        'value' =>Html::img(
                            $model->getUploadUrl('goods_list'),
                            ['height' => '200']
                        ) ?: '图片未上传'
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
