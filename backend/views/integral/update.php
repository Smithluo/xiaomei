<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Integral */

$this->title = '积分编辑: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '积分列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="integral-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'statusMap' => $statusMap,
        'payCodeMap' => $payCodeMap,
    ]) ?>

</div>
