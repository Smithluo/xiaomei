<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\FullCutRule */

$this->title = $model->rule_id;
$this->params['breadcrumbs'][] = ['label' => '满减/优惠券 规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="full-cut-rule-view">
    <p>
        <?= Html::a('创建 满减/优惠券 规则', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= $this->render('_form', [
        'model'                 => $model,
        'coupon'                => $coupon,
        'eventList'             => $eventList,
        'eventType'             => $eventType,
        'couponRecordIssueForm' => $couponRecordIssueForm,
    ]) ?>

</div>
