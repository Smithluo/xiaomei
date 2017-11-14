<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\GalleryImg */

$this->title = $model->img_id;
$this->params['breadcrumbs'][] = ['label' => 'Gallery Imgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$imgOriginal = $model->getUploadUrl('img_original');
?>
<div class="gallery-img-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->img_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->img_id], [
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
            'img_id',
            [
                'attribute' => 'gallery_id',
                'label' => '相册名称',
                'format' => 'raw',
                'value' => $model->gallery->gallery_name,
            ],
            [
                'attribute' => 'img_url',
                'label' => '显示图',
                'format' => 'raw',
                'value' => Html::img($model->getUploadUrl('img_url'), ['height' => 100])
            ],
            [
                'attribute' => 'img_original',
                'label' => '原图路径(点击看大图)',
                'format' => 'raw',
                'value' => Html::a(Html::img($imgOriginal, ['height' => 200]), $imgOriginal)
            ],
            'img_desc',
            'sort_order',
        ],
    ]) ?>

</div>

<p>
    <?= Html::a('上传图片', ['create'], ['class' => 'btn btn-success']) ?>
</p>