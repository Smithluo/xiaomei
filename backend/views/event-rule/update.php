<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventRule */

$this->title = '编辑活动策略: ' . $model->rule_id;
$this->params['breadcrumbs'][] = ['label' => '活动策略列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->rule_id, 'url' => ['view', 'id' => $model->rule_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="event-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'giftEventMap' => $giftEventMap,
    ]) ?>

</div>
