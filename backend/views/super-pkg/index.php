<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SuperPkgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '超值礼包';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="super-pkg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建超值礼包', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pag_name',
            'pag_desc',
            [
                'attribute' => 'gift_pkg_id',
                'value' => function($model) use ($giftPkgList) {
                    return !empty($giftPkgList[$model->gift_pkg_id]) ? $giftPkgList[$model->gift_pkg_id] : '';
                }
            ],
            'sort_order',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_show',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/super-pkg/edit-show'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'start_time',
                'value' =>function($model)
                {
                    return $model->start_time;
                }
            ],
            [
                'attribute' => 'end_time',
                'value' =>function($model)
                {
                    return $model->end_time;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
