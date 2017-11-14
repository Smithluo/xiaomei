<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;
use backend\models\Event;
use backend\models\AdminUser;
use backend\models\EventRule;
use backend\models\GoodsPkg;

/* @var $this yii\web\View */
/* @var $model common\models\Event */

$this->title = $model->event_name;
$this->params['breadcrumbs'][] = ['label' => '活动', 'url' => ['index']];
?>
<div class="event-view">
    <?= Html::a('导出参与过该活动的用户手机','/event/export-users-mobile?eventId='.$model->event_id,['class'=>'btn btn-success'])?>
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

