<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ZhifaBrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zhifa Brands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhifa-brand-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Zhifa Brand', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    return $model['brand']['brand_name'];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '品牌',
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \common\models\Brand::getBrandListMap(),
                        ],
                        'formOptions' => [
                            'action' => ['/zhifa-brand/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\Brand::getBrandListMap(),
                    'options' => ['placeholder' => '品牌'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/zhifa-brand/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
                'filter' => false,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
