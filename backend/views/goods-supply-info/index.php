<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GoodsSupplyInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Supply Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-supply-info-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php $form = \yii\widgets\ActiveForm::begin([
            'action' => ['import'],
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'
            ]])
        ?>

        <?= $form->field($importModel, 'file')->fileInput() ?>

        <button>提交</button>

        <?php \yii\widgets\ActiveForm::end() ?>
    </p>

    <p>
        <?php
            echo Html::a('导出', \yii\helpers\Url::to('export'));
        ?>
    </p>

    <?php

    $columns = [
        [
            'attribute' => 'goods_id',
            'format' => 'raw',
            'value' => function($model){
                if ($model->extension_code == 'integral_exchange') {
                    $url = Yii::$app->params['pcHost'].'/exchange.php?act=info&id='.$model->goods_id;
                } else {
                    $url = Yii::$app->params['pcHost'].'/goods.php?id='.$model->goods_id;
                }
                return '<a target="_blank" href="'.$url.'">'.$model->goods_id.'</a>';
            }
        ],
        'goods_name',
        'goods_sn',
        'measure_unit',
        'goods_weight',
        'market_price',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'supplyPrice',
            'value' => function ($model) {
                if (empty($model->supplyInfo)) {
                    return null;
                }
                return $model->supplyInfo->supply_price;
            },
            'editableOptions' => function ($model, $key, $index, $column) {
                return [
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods-supply-info/edit-supply-price'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'min_price',
        'shop_price',
        [
            'attribute' => 'brand_id',
            'value' => function($model){
                if (empty($model->brand)) {
                    return null;
                }
                return $model->brand->brand_name;
            }
        ],
    ];

    echo DynaGrid::widget([
//        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">商品列表</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                        ['index'],
                        ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-goods-supply-info',
        ],
    ]); ?>
</div>
