<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attributes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Attribute', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'attr_id',
            [
                'label'=>'所属商品类型',
                'attribute'=>'cat_id',
                'value'=>function($model) {
                    return $model->goodsType->cat_name;
                },
            ],
            'attr_name',
            [
                'attribute' => 'attr_input_type',
                'value' => function($model) {
                    return $model->inputTypeString();
                }
            ],
            [
                'attribute' => 'attr_type',
                'value' => function($model) {
                    return $model->attrTypeString();
                }
            ],
            // 'attr_values:ntext',
            // 'attr_index',
            // 'sort_order',
            // 'is_linked',
            // 'attr_group',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
