<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarUrl */

$this->title = '更新楼层链接: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Index Star Urls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-star-url-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
