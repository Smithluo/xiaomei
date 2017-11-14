<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexHotGoods */

$this->title = '新建热销商品';
$this->params['breadcrumbs'][] = ['label' => '热销商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-hot-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
