<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ServicerDivideRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '服务商分成流水列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicer-divide-record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?=

    DynaGrid::widget([
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'order_id',
                'value' => function ($model) {
                    return $model->orderInfo->order_sn;
                }
            ],
            [
                'class'=>'kartik\grid\ExpandRowColumn',
                'width'=>'50px',
                'value'=>function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail'=>function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_goods-list', ['model'=>$model->orderInfo]);
                },
                'headerOptions'=>['class'=>'kartik-sheet-style'],
            ],
            'amount',
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    if (isset($model->user)) {
                        return $model->user->showName. '('. $model->user->mobile_phone. ')';
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'servicer_user_id',
                'value' => function ($model) {
                    if (isset($model->servicer)) {
                        return $model->servicer->nickname. '('. $model->servicer->mobile_phone. ')';
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'parent_servicer_user_id',
                'value' => function ($model) {
                    if (isset($model->parentServicer)) {
                        return $model->parentServicer->nickname. '('. $model->parentServicer->mobile_phone. ')'. '('. $model->parentServicer->company_name. ')';
                    }
                    return null;
                }
            ],
            'divide_amount',
            'parent_divide_amount',
            'money_in_record_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">服务商分成列表</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-servicer-divide-record',
        ],
    ]); ?>
</div>
