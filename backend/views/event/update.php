<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = '更新活动: ' . $model->event_id;
$this->params['breadcrumbs'][] = ['label' => '活动列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->event_id, 'url' => ['view', 'id' => $model->event_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-update">
    <?= $this->render('_form', [
        'model' => $model,
        'ruleMap' => $ruleMap,
        'ruleLink' => $ruleLink,
        'goodsList' => $goodsList,
        'goodsNameList' => $goodsNameList,
        'goodsBrandList' => $goodsBrandList,
        'selectedBrandList' => $selectedBrandList,
        'eventTypeMap' => $eventTypeMap,
        'effectiveScopeTypeMap' => $effectiveScopeTypeMap,
        'autoDestroyMap' => $autoDestroyMap,
        'receiveTypeMap' => $receiveTypeMap,
    ]) ?>

</div>
