<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexActivity */

$this->title = '编辑首页分类: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '首页分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="index-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
