<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;
use backend\models\EventRule;

/* @var $this yii\web\View */
/* @var $model common\models\EventRule */

$this->title = $model->rule_name;
$this->params['breadcrumbs'][] = ['label' => '活动策略', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-rule-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->rule_id], ['class' => 'btn btn-primary']) ?>
        <?php
//            echo Html::a('删除', ['delete', 'id' => $model->rule_id], [
//                'class' => 'btn btn-danger',
//                'data' => [
//                    'confirm' => '确定要删除这条策略吗?',
//                    'method' => 'post',
//                ],
//            ]);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'rule_id',
            'rule_name',
            [
                'attribute' => 'match_type',
                'value' => EventRule::$match_type_map[$model->match_type]
            ],
            'match_value',
            [
                'attribute' => 'match_effect',
                'value' => EventRule::$match_effect_map[$model->match_effect]
            ],
            'gift_id',
            'gift_num',
            'gift_show_peice',
            'gift_need_pay',
            [
                'attribute' => 'updated_at',
                'value' => DateTimeHelper::getFormatCNDateTime($model->updated_at)
            ],
            [
                'attribute' => 'event_id',
                'value' => $model->event->event_name
            ],
        ],
    ]) ?>

</div>
