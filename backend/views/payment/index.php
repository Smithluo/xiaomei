<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'pay_id',
                'options' => [
                    'style' => 'max-width: 30px;',
                ]
            ],
            'pay_code',
            'pay_name',
            'pay_fee',
//            [
//                'attribute' => 'pay_desc',
////                'headerOptions' => ['style' => 'width: 100px'],
//                'format'=>'raw',
//                'contentOptions' => ['style' => 'max-width: 100px; height: 100px; word-wrap: break-word;'],
//            ],
            // 'pay_order',
            // 'pay_config:ntext',
            // 'enabled',
            // 'is_cod',
            // 'is_online',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
