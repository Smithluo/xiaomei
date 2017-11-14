<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '运营品牌管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('导出品牌', ['export'], ['class' => 'btn btn-success']) ?>
    </p>

    <p>
        <?php $form = \yii\widgets\ActiveForm::begin([
            'action' => ['import-discount'],
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'
            ]])
        ?>

        <?= $form->field($discountImportModel, 'file')->fileInput() ?>

        <button>提交</button>

        <?php \yii\widgets\ActiveForm::end() ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'brand_id',
            'brand_name',
            'is_show',
            'brand_depot_area',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'brand_desc',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '品牌描述',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-brand/edit-desc'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'short_brand_desc',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '短描述',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-brand/edit-short-desc'],
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
                        'header' => '排序',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-brand/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'brand_tag',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '热门品牌序号',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/operation-brand/edit-tag'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'discount',
        ],
    ]); ?>
</div>
