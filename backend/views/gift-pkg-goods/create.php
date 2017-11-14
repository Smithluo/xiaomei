<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkgGoods */

$this->title = '新增礼包商品';
$this->params['breadcrumbs'][] = ['label' => '礼包商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'giftPkgList' => $giftPkgList,
        'goodsList' => $goodsList,
    ]) ?>

</div>
