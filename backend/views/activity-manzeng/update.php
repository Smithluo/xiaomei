<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ActivityManzeng */

$this->title = '更新活动满赠商品: ' . $model->goods_id;
$this->params['breadcrumbs'][] = ['label' => '活动满赠商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->goods_id, 'url' => ['view', 'id' => $model->goods_id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="activity-manzeng-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
