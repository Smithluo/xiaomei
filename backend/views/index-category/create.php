<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexActivity */

$this->title = '配置首页分类';
$this->params['breadcrumbs'][] = ['label' => '首页分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
