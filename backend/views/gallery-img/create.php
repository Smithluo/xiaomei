<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\GalleryImg */

$this->title = '上传图片';
$this->params['breadcrumbs'][] = ['label' => 'Gallery Imgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-img-create">


    <?= $this->render('_form', [
        'model' => $model,
        'galleryMap' => $galleryMap,
    ]) ?>

</div>
