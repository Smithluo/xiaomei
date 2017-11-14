<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FullCutRule */

$this->title = 'Update Full Cut Rule: ' . $model->rule_id;
$this->params['breadcrumbs'][] = ['label' => '满减/优惠券 规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->rule_id, 'url' => ['view', 'id' => $model->rule_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="full-cut-rule-update">

    <?= $this->render('_form', [
        'model'                 => $model,
        'coupon'                => $coupon,
        'eventList'             => $eventList,
        'eventType'             => $eventType,
        'couponRecordIssueForm' => $couponRecordIssueForm,
    ]) ?>

</div>
