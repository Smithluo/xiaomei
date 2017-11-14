<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkg */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '礼包活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-create">

    <?= $this->render('_form', [
        'model' => $model,
        'shippingList' => $shippingList,
        'isOnSaleMap' => $isOnSaleMap,
        'goodsList' => $goodsList,
    ]) ?>

</div>
