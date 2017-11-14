<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\GalleryImg */

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Gallery Imgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->img_id, 'url' => ['view', 'id' => $model->img_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gallery-img-update">

    <?= $this->render('_form', [
        'model' => $model,
        'galleryMap' => $galleryMap,
    ]) ?>

</div>
