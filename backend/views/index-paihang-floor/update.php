<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangFloor */

$this->title = '更新热销排行品类: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '热销品类列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-paihang-floor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
