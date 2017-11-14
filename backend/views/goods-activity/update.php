<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsActivity */

$this->title = '更新: ' . $act_type_map[$model->act_type].' | '.$model->act_name;
$this->params['breadcrumbs'][] = ['label' => $act_type_map[$model->act_type], 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->act_name, 'url' => ['view', 'id' => $model->act_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'shippingCodeNameMap' => $shippingCodeNameMap,
        'allGoodsList' => $allGoodsList,
    ]) ?>

</div>
