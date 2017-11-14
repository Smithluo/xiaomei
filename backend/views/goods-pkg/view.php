<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsPkg */

$this->title = $model->pkg_id;
$this->params['breadcrumbs'][] = ['label' => '商品包', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-pkg-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->pkg_id], ['class' => 'btn btn-primary']) ?>
        <?php
//            echo Html::a('删除', ['delete', 'id' => $model->pkg_id], [
//                'class' => 'btn btn-danger',
//                'data' => [
//                    'confirm' => 'Are you sure you want to delete this item?',
//                    'method' => 'post',
//                ],
//            ]);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pkg_id',
            'pkg_name',
            'allow_goods_list:ntext',
            'deny_goods_list:ntext',
            [
                'attribute' => 'updated_at',
                'value' => DateTimeHelper::getFormatCNDateTime($model->updated_at)
            ], 
        ],
    ]) ?>

</div>
