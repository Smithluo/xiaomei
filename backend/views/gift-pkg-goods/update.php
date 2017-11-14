<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkgGoods */

$this->title = '编辑礼包商品: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '礼包商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gift-pkg-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'giftPkgList' => $giftPkgList,
        'goodsList' => $goodsList,
    ]) ?>

</div>
