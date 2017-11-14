<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallery */

$this->title = $model->img_id;
$this->params['breadcrumbs'][] = ['label' => '商品相册', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-gallery-view">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <p>
        <?= Html::a('删除', ['delete', 'id' => $model->img_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '删除后图片无法找回，确定要删除吗？',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
