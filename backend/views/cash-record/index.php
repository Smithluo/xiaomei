<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CashRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cash Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Cash Record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'cash',
            [
                'label'=>'用户名',
                'attribute'=>'user_name',
                'value'=>function($model) {
                    return $model->user->showName;
                },
            ],
            [
                'label'=>'开户人姓名',
                'attribute'=>'bank_user_name',
                'value'=>function($model) {
                    return $model->user->bank_info_id > 0 ? $model->user->bankinfo->user_name : '';
                },
            ],
            [
                'label'=>'开户银行名称',
                'attribute'=>'bank_name',
                'value'=>function($model) {
                    return $model->user->bank_info_id > 0 ? $model->user->bankinfo->bank_name : '';
                },
            ],
            [
                'label'=>'银行卡账号',
                'attribute'=>'bank_card_no',
                'value'=>function($model) {
                    return $model->user->bank_info_id > 0 ? $model->user->bankinfo->bank_card_no : '';
                },
            ],
            [
                'label'=>'银行地址',
                'attribute'=>'bank_address',
                'value'=>function($model) {
                    return $model->user->bank_info_id > 0 ? $model->user->bankinfo->bank_address : '';
                },
            ],
            [
                'label'=>'备注',
                'attribute'=>'note',
            ],
            [
                'label'=>'汇款时间',
                'attribute'=>'pay_time',
                'value'=>function($model) {
                    if (!empty($model) && !empty($model->pay_time)) {
                        return $model->pay_time;
                    }
                    return null;
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
