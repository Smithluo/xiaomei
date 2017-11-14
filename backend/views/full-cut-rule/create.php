<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\FullCutRule */

$this->title = '创建 满减/优惠券 规则';
$this->params['breadcrumbs'][] = ['label' => '满减/优惠券 规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="full-cut-rule-create">

    <?= $this->render('_form', [
        'model' => $model,
        'eventList' => $eventList,
    ]) ?>

</div>
