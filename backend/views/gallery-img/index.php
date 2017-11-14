<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GalleryImgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gallery Imgs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-img-index">
    <?php  echo $this->render(
            '_search',
            [
                'model' => $searchModel,
                'galleryMap' => $galleryMap,
            ]
    ); ?>

    <p>
        <?= Html::a('上传图片', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'img_id',
            [
                'attribute' => 'gallery_id',
                'label' => '相册名称',
                'value' => function($model) {
                    return $model->gallery->gallery_name;
                }
            ],
            [
                'attribute' => 'img_url',
                'label' => '显示图',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::img($model->getUploadUrl('img_url'), ['height' => 75]);
                }
            ],
            [
                'attribute' => 'img_original',
                'label' => '原图路径(点击看大图)',
                'format' => 'raw',
                'value' => function($model) {
                    $imgOriginal = $model->getUploadUrl('img_original');
                    return Html::a(Html::img($imgOriginal, ['height' => 75]), $imgOriginal);
                }
            ],
            'img_desc',
             'sort_order',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
