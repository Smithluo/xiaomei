<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Goods;
use common\helper\ImageHelper;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallery */
/* @var $form yii\widgets\ActiveForm */

$goodsList = Goods::getGoodsMap();
$action = Yii::$app->controller->action->id;
?>

<div class="goods-gallery-form">

    <?php
        if ($model->img_id) {
            $goods_name = $model->goods['goods_name'];
        } else {
            $goods_name = '';
        }

        $attributes = [
            [
                'group' => true,
                'label' => '商品轮播图',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'goods_id',
                        'label' => '商品名称',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 5%',
                        ],
                    ],
                    [
                        'attribute' => 'img_original',
                        'format' => 'raw',
                        'value' => Html::img(
                            ImageHelper::get_image_path($model->img_original),
                            ['style' => 'width: 295px;height: 295px;']
                        ),
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'type' => DetailView::INPUT_FILE,
                    ],
                    [
                        'attribute' => 'img_desc',
                        'label' => '图片描述',
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 20%',
                        ],
                    ],
                    [
                        'attribute' => 'img_desc',
                        'displayOnly' => true,
                        'label' => '图片路径',
                        'format' => 'raw',
                        'value' => '原图：'.ImageHelper::get_image_path($model->img_original).'<br /><br />'.
                            '显示图：'.ImageHelper::get_image_path($model->img_url).'<br /><br />'.
                            '缩略图：'.ImageHelper::get_image_path($model->thumb_url),
                        'labelColOptions' => [
                            'style' => 'width: 5%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 50%',
                        ],
                    ],
                ]
            ],
        ];

        $action = Yii::$app->controller->action->id;
        echo DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
            'mode' => $action == 'view' ? DetailView::MODE_VIEW : DetailView::MODE_EDIT,
            'deleteOptions'=>[ // your ajax delete parameters
                'params' => ['id' => $model->goods_id, 'custom_param' => true],
            ],
            'panel'=>[
                'heading'=>'商品轮播图 -- ' . $goods_name,
                'type'=>DetailView::TYPE_PRIMARY,
            ],
            'formOptions' => [
                'action' => $action == 'create'
                    ? (!empty($model->goods_id)
                        ? \yii\helpers\Url::to(['create', 'goods_id' => $model->goods_id])
                        : \yii\helpers\Url::to(['create'])
                    )
                    : \yii\helpers\Url::to(['update', 'id' => $model->img_id]),
            ],

            'buttons1' => '{update}',
        ]);
    ?>

</div>
