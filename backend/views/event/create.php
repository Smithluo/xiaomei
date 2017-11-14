<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = '创建活动';
$this->params['breadcrumbs'][] = ['label' => '活动列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3 style="color: red">tips: 不配置 参与活动的商品 等价于 商城活动没已生效 —— 当前不支持全局的满减、优惠券活动</h3>
<div class="event-create">
    <?= $this->render('_form', [
        'model' => $model,
        'ruleMap' => $ruleMap,
        'ruleLink' => $ruleLink,
        'goodsList' => $goodsList,
        'goodsNameList' => $goodsNameList,
        'goodsBrandList' => $goodsBrandList,
        'eventTypeMap' => $eventTypeMap,
        'effectiveScopeTypeMap' => $effectiveScopeTypeMap,
        'autoDestroyMap' => $autoDestroyMap,
        'receiveTypeMap' => $receiveTypeMap,
    ]) ?>

</div>
