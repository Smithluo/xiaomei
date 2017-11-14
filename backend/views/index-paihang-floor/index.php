<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexPaihangFloorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '排行品类设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-paihang-floor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增排行品类', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-paihang-floor/edit-title'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'description:ntext',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::img($model->getUploadUrl('image'));
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-paihang-floor/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
