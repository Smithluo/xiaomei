<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IndexFullCutGoods */

$this->title = '添加首页显示的满减商品';
$this->params['breadcrumbs'][] = ['label' => '首页显示的满减商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-full-gift-goods-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
