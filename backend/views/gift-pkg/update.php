<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkg */

$this->title = '编辑礼包活动: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '礼包活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gift-pkg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'shippingList' => $shippingList,
        'isOnSaleMap' => $isOnSaleMap,
        'goodsList' => $goodsList,
        'giftGoodsList' => $giftGoodsList,
    ]) ?>

</div>
