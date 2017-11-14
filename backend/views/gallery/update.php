<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Gallery */

$this->title = '编辑相册: ' . $model->gallery_id;
$this->params['breadcrumbs'][] = ['label' => '相册', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->gallery_id, 'url' => ['view', 'id' => $model->gallery_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gallery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'moreGalleryImg' => $moreGalleryImg,
        'galleryImgList' => $galleryImgList,
    ]) ?>

</div>
