<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarGoodsTabConf */

$this->title = '更新首页楼层配置: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '首页楼层配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-star-goods-tab-conf-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
