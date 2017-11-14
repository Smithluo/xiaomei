<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;
use backend\models\Goods;
use backend\models\UserRank;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = ' 商品ID：'.$model->goods_id.' | 商品名称：'.$model->goods_name;
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="goods-view">

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
        'spuName' => $spuName,
        'spuMap' => $spuMap,
    ]) ?>

    <?= $this->render('_lock_stock', [
        'model' => $model,
        'newStockLock' => $newStockLock,
    ]) ?>

</div>
