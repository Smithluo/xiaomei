<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallery */

$this->title = '更换轮播图: ' .  $model->goods['goods_name'];
$this->params['breadcrumbs'][] = ['label' => '图片列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->img_id, 'url' => ['view', 'id' => $model->img_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-gallery-update">

    <h1><?= Html::encode($this->title) ?></h1>

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
