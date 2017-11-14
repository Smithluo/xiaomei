<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Goods;
use common\helper\ImageHelper;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallery */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="goods-gallery-form">

    <?php
        $columns = [];
        if ($modelList) {
            foreach ($modelList as $img_id => $model) {
                $columns[] = [
                    'columns' => [
                        [
                            'attribute' => '['.$img_id.']goods_id',
                            'value' => $goods['goods_name'], //  ,
                            'label' => '商品名称',
                            'labelColOptions' => [
                                'style' => 'width: 10%',
                            ],
                            'valueColOptions' => [
                                'style' => 'width: 30%',
                            ],
                        ],
                        [
                            'attribute' => 'img_original',
                            'format' => 'raw',
                            'value' => ImageHelper::get_image_path($model->img_original),
                            'labelColOptions' => [
                                'style' => 'width: 10%',
                            ],
                            'valueColOptions' => [
                                'style' => 'width: 20px',
                            ],
                            'type' => DetailView::INPUT_FILE,
                        ],
                        [
                            'attribute' => 'img_desc',
                            'label' => '图片描述',
                            'labelColOptions' => [
                                'style' => 'width: 10%',
                            ],
                            'valueColOptions' => [
                                'style' => 'width: 20%',
                            ],
                        ],
                        'viewModel' => $model,
                        'editModel' => $model,
                    ]
                ];
            }
        }

        $attributes = array_merge(
            [
                'group' => true,
                'label' => '商品轮播图',
                'rowOptions' => [
                    'class' => 'info',
                ],
            ],
            $columns
        );

        $action = Yii::$app->controller->action->id;
        echo DetailView::widget([
            'model' => $modelList,
            'attributes' => $attributes,
            'mode' =>  DetailView::MODE_EDIT,

            'panel'=>[
                'heading'=>'商品轮播图 -- ' . $goods['goods_name'],
                'type'=>DetailView::TYPE_PRIMARY,
            ],
            'formOptions' => [
                'action' => \yii\helpers\Url::to(['list', 'goodsId' => $goods['goods_id']]),
            ],

            'buttons1' => $action == 'create' ? '{create}' : '{update}',
            //            'buttons1' => '{update}',
            //  Yii::$app->controller->action->id == 'update' ? '{update}' : '',
        ]);
    ?>

</div>
