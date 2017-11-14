<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GallerySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '相册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Gallery', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'gallery_id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'gallery_name',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '相册名',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/gallery/edit-value'],
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
                        'header' => '排序值',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/gallery/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_show',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否显示',
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/gallery/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{viewImgList} {update} {delete}',
                'buttons' => [
                    'viewImgList' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/gallery-img/index?GalleryImgSearch[gallery_id]='.$model->gallery_id);
                    },
                ]
            ]
        ],
    ]); ?>
</div>
