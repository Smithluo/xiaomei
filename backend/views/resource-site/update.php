<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ResourceSite */

$this->title = '编辑 资源站点: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Resource Sites', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="resource-site-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
