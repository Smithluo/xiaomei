<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexFullCutGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '添加首页显示的满减商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-full-cut-goods-view">

    <?= $this->render('_form', [
        'model' => $model,
        'goodsList' => $goodsList,
        'goodsName' => $goodsName,
    ]) ?>

</div>

<div class="row">
    <p>
        <?= Html::a('添加首页显示的满减商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
