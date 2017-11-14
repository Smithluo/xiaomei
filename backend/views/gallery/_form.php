<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\ImageHelper;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\models\Gallery */
/* @var $form yii\widgets\ActiveForm */

if (Yii::$app->user->can('/gallery-img/update')) {
    $catUpdate = true;
} else {
    $catUpdate = false;
}
?>

<?php
    $goodsGalleryListColumns = [];
    if (!empty($galleryImgList)) {
        foreach ($galleryImgList as $key => $galleryImg) {
            $imgOriginal = ImageHelper::get_image_path($galleryImg->img_original);
            $goodsGalleryListColumns[]['columns'] = [
                [
                    'attribute' => '['.$galleryImg->img_id.']img_original',
                    'label' => '原图',
                    'format' => 'raw',
                    'value' => !empty($galleryImg->img_original)
                        ? Html::img($imgOriginal, ['style' => 'width:200px; height:200px'])
                        : '请上传',
                    'type' => DetailView::INPUT_FILE,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'viewModel' => $galleryImg,
                    'editModel' => $galleryImg,
                ],
                [
                    'attribute' => '['.$galleryImg->img_id.']img_desc',
                    'label' => '图片描述',
                    'format' => 'raw',
                    'value' => $galleryImg->img_desc,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'viewModel' => $galleryImg,
                    'editModel' => $galleryImg,
                ],
                [
                    'attribute' => '['.$galleryImg->img_id.']img_url',
                    'label' => '图片路径',
                    'format' => 'raw',
                    'value' =>
                        '原　图：'.(!empty($galleryImg->img_original) ? $imgOriginal : '未上传').'<br /><br />'.
                        '缩略图：'.ImageHelper::get_image_path($galleryImg->img_url),
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 25%',
                    ],
                    'viewModel' => $galleryImg,
                    'editModel' => $galleryImg,
                ],
                [
                    'attribute' => '['.$key.']sort_order',
                    'label' => '排序值',
                    'format' => 'raw',
                    'value' => $galleryImg->sort_order,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'viewModel' => $galleryImg,
                    'editModel' => $galleryImg,
                ],
                [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function () use ($galleryImg, $model) {
                        return Html::a('删除', Url::to(['gallery-img/delete', 'id' => $galleryImg['img_id']]));
                    },
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'viewModel' => $galleryImg,
                    'editModel' => $galleryImg,
                ],
            ];
        }
    }

    if ($moreGalleryImg) {
        $goodsGalleryListColumns[]['columns'] = [
            [
                'attribute' => 'img_original',
                'label' => '新增图片',
                'value' => '待上传',
                'type' => DetailView::INPUT_FILE,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 15%',
                ],
                'viewModel' => $moreGalleryImg,
                'editModel' => $moreGalleryImg,
            ],
            [
                'attribute' => 'img_desc',
                'label' => '图片描述',
                'format' => 'raw',
                'value' => $moreGalleryImg->img_desc,
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 30%',
                ],
                'viewModel' => $moreGalleryImg,
                'editModel' => $moreGalleryImg,
            ],
            [
                'attribute' => 'sort_order',
                'label' => '排序值',
                'format' => 'raw',
                'value' => $moreGalleryImg->sort_order,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 15%',
                ],
                'viewModel' => $moreGalleryImg,
                'editModel' => $moreGalleryImg,
            ],
            [
                'attribute' => 'img_url',
                'label' => '图片路径',
                'value' => '待上传',
                'displayOnly' => true,
                'labelColOptions' => [
                    'style' => 'width: 10%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 15%',
                ],
                'viewModel' => $moreGalleryImg,
                'editModel' => $moreGalleryImg,
            ]
        ];

    }

    $attributes = [
        [
            'columns' => [
                [
                    'attribute' => 'gallery_name',
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'sort_order',
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'is_show',
                    'type' => DetailView::INPUT_SWITCH,
                    'value' => $model->is_show ? '显示' : '不显示',
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => '显示',
                            'offText' => '不显示',
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ],
    ];
//    $attributes = ArrayHelper::merge($attributes, $goodsGalleryListColumns);
?>

<div class="gallery-form">
    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'enableEditMode' => true,   //  $catUpdate
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->gallery_id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading'=>'图片列表',
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'formOptions' => [
            'action' => $model->isNewRecord
                ? \yii\helpers\Url::to(['create'])
                : \yii\helpers\Url::to(['update', 'id' => $model->gallery_id]),
        ],

        'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
        //            'buttons1' => '{update}',
        //  Yii::$app->controller->action->id == 'update' ? '{update}' : '',
    ]);
    ?>
</div>
