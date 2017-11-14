<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Spu */

$this->title = '更新SPU，请注意检查关联当前SPU的商品是否正确';
$this->params['breadcrumbs'][] = ['label' => 'SPU列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="spu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'skuGoodsList' => $skuGoodsList,
        'isOnSaleMap' => $isOnSaleMap,
    ]) ?>

</div>

