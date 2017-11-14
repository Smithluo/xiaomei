<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IntegralSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '积分流水';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integral-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'statusMap' => $statusMap,
        'payCodeMap' => $payCodeMap,
    ]); ?>

    <p>
        <?= Html::a('手动 赠送/扣除 积分', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'integral',
            'user_id',
            [
                'attribute' => 'pay_code',
                'value' => function ($model) use ($payCodeMap){
                    return $payCodeMap[$model->pay_code];
                }
            ],
            'out_trade_no',
            'note',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return DateTimeHelper::getFormatCNDateTime($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return DateTimeHelper::getFormatCNDateTime($model->updated_at);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) use ($statusMap){
                    return $statusMap[$model->status];
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
</div>
