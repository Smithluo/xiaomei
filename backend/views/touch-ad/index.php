<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TouchAdSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '广告列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-ad">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create TouchAd', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ad_id',
            [
                'attribute' => 'position_id',
                'value' => function($model) {
                    return $model->touchAdPosition->position_name;
                },
                'options' => ['style' => 'width: 250px;'],
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\TouchAdPosition::getTouchAdPositionList(),
                    'options' => ['placeholder' => '广告位'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'attribute' => 'media_type',
                'value' => function($model) {
                    return \common\models\TouchAd::$mediaTypeMap[$model->media_type];
                }
            ],
            'ad_name',
            'ad_link',
//            [
//                'attribute' => 'ad_code',
//                'value' => function($model) {
////                    return $model->getUploadUrl('ad_code');
//                    return Html::img($model->getUploadUrl('ad_code'));
//                }
//            ],
            [
                'attribute' => 'start_time',
                'value' => function($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDate($model->start_time);
                }
            ],
//            'end_time:datetime',
            [
                'attribute' => 'end_time',
                'value' => function($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDate($model->end_time);
                }
            ],
            'click_count',

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'enabled',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'size' => 'sm',
                        'data' => \common\models\TouchAd::$enableMap,
                        'formOptions' => [
                            'action' => ['/touch-ad/editValue'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,

                    ];
                },
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\TouchAd::$enableMap[$model->enabled];
                },
                'pageSummary' => true,
            ],
            'ad_index',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
