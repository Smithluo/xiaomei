<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Goods;
use kartik\detail\DetailView;
use backend\models\Brand;
use backend\models\Category;
use backend\models\Users;
use backend\models\GoodsGallery;
use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\TextHelper;
use backend\models\Shipping;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$this->title = '创建商品';
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="goods-create">
    <p style="color: red">
        <strong>创建品牌前先搜索一下品牌是否已存在  ！！！复制的商品需要重新上传主图和轮播图 ！！！</strong>
    </p>
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
    ]) ?>
</div>

