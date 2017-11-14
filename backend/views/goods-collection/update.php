<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsCollection */

$this->title = '更新选品专辑: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Goods Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-collection-update">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= $this->render('_goods_list', [
        'model' => $model,
        'itemDataProvider' => $itemDataProvider,
        'itemSearchModel' => $itemSearchModel,
    ]) ?>

    <?=
        $this->render('_create_goods', [
            'model' => $model,
            'newItemList' => $newItemList,
        ])
    ?>

</div>
