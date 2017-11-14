<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FullCutRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '满减/优惠券 规则列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="full-cut-rule-index">
    <?php  echo $this->render('_search', [
            'model'     => $searchModel,
            'eventList' => $eventList,
    ]); ?>

    <div class="row">
        <?= Html::a('创建 满减/优惠券 规则', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?php
        $columns = [
            'rule_id',
            'rule_name',
            [
                'attribute' => 'event_id',
                'value' => function ($model) use ($eventList) {
                    return $eventList[$model->event_id];
                }
            ],
            'above',
            'cut',
            [
                'attribute' => 'status',
                'value' => function ($model) use ($isActiveMap) {
                    return $isActiveMap[$model->status];
                }
            ],
            [
                'attribute' => 'term_of_validity',
                'value' => function ($model) {
                    return $model->term_of_validity > 0
                        ? $model->term_of_validity
                        : '与领取优惠券时的活动生效时段一致';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
            ],
        ];

        echo DynaGrid::widget([
    //        'dataProvider' => $dataProvider,
    //        'filterModel' => $searchModel,
            'columns' => $columns,
            'storage' => DynaGrid::TYPE_COOKIE,
            'theme' => 'panel-primary',
            'gridOptions' => [
                'dataProvider' => $dataProvider,
    //            'filterModel' => $searchModel,
                'panel' => [
                    'heading' => '<h3 class="panel-title">满减/优惠券 规则列表</h3>',
                ],
                'toolbar' =>  [
                    ['content'=>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                            ['index'],
                            ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                    ],
                    ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                    '{toggleData}',
                ]
            ],
            'options' => [
                'id' => 'dynagrid-full-cut-rule-index',
            ],
        ]);

    ?>

</div>
