<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = '编辑商品信息: ' . $model->goods_name;
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->goods_name, 'url' => ['view', 'id' => $model->goods_id]];
?>
<div class="goods-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allUserRank' => $allUserRank,
        'shippingList' => $shippingList,
        'suppliers' => $suppliers,
        'tags_str' => $tags_str,
        'tagsMap' => $tagsMap,
        'allTagIds' => $allTagIds,
        'goodsGallery' => $goodsGallery,
        'GoodsGalleryList' => $GoodsGalleryList,  //  在商品详情页显示多个轮播图的处理
        'moreGalleryList' => $moreGalleryList,
        'spuMap' => $spuMap,
        'spuName' => $spuName,
    ]) ?>

    <?= $this->render('_lock_stock', [
        'model' => $model,
        'newStockLock' => $newStockLock,
    ]) ?>

</div>
