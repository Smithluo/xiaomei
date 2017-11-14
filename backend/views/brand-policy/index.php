<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BrandPolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '品牌增值政策';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Brand Policy', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    return $model->brand->brand_name;
                },
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => function($model, $key, $index) use ($brands) {
                    return [
                        'size' => 'lg',
                        'header' => '选择品牌',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        //TODO 此处项需要一起配置，带有搜索下拉框选择
                        'widgetClass' => 'kartik\editable\Select2',
                        'options' => [
                            'data' => $brands,
                        ],
                        'formOptions' => [
                            'method' => 'post',
                            'action' => \yii\helpers\Url::to('/brand-policy/editValue'),
                        ],
                    ];
                },

            ],
            [
                'attribute' => 'policy_content',
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'size' => 'lg',
                        'formOptions' => [
                            'action' => ['/brand-policy/editValue'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'policy_link',
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/brand-policy/editValue'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' =>'sort_order',
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/brand-policy/editValue'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'status',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'size' => 'sm',
                        'data' => \common\models\BrandPolicy::$statusMap,
                        'formOptions' => [
                            'action' => ['/brand-policy/editValue'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,

                    ];
                },
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\BrandPolicy::$statusMap[$model->status];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
