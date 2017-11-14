<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexStarBrandConfSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页楼层品牌配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-brand-conf-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建首页楼层品牌配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'brand_id',
                'value' => function ($model) {
                    if (!empty($model->brand)) {
                        return $model->brand->brand_name;
                    }
                    return null;
                }
            ],
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
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-star-brand-conf/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
