<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexSpecConfig */

$this->title = '更新首页活动专区配置: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Index Spec Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-spec-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'allGoods' => $allGoods,
    ]) ?>

</div>
