<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexHotBrand */

$this->title = '更新热门品牌: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '热门品牌列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="index-hot-brand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
