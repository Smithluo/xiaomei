<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexStarUrlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '楼层链接(显示在PC站楼层左侧)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-url-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建链接', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'tab_id',
                'value' => function ($model) {
                    if (!empty($model->tab)) {
                        return $model->tab->tab_name;
                    }
                    return null;
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-url/edit-title'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'url',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-url/edit-url'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-url/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
