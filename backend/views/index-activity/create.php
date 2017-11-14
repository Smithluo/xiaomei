<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexActivity */

$this->title = '配置活动特惠';
$this->params['breadcrumbs'][] = ['label' => '活动特惠', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
