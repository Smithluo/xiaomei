<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallery */

$this->title = '新增图片';
$this->params['breadcrumbs'][] = ['label' => '图片列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-gallery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
