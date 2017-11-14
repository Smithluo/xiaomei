<?php

use yii\helpers\Html;
use common\widgets\GridView;

\service\assets\CashRecordAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\CashRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收支对账';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/statement';
?>
<div class="wrapper wrapper-content animated fadeInRight">

<div class="row animated fadeInRight">
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right"></span>
                <h5>近30天收支</h5>
            </div>
            <div class="ibox-content">

                <div class="row">
                    <div class="col-md-6">
                        <h2 class="no-margins text-danger">￥ <?= $monthInCash ?></h2>
                        <small>近30天收入</small><i class="fa fa-level-up"></i>
                    </div>
                    <div class="col-md-6">
                        <h2 class="no-margins text-navy">￥ <?= $monthOutCash ?></h2>
                        <small>近30天支出</small><i class="fa fa-level-up"></i>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right"></span>
                <h5>总收支</h5>
            </div>
            <div class="ibox-content">

                <div class="row">
                    <div class="col-md-6">
                        <h2 class="no-margins text-danger">￥ <?= $inCash ?></h2>
                        <small>总收入</small><i class="fa fa-level-up"></i>
                    </div>
                    <div class="col-md-6">
                        <h2 class="no-margins text-navy">￥ <?= $outCash ?></h2>
                        <small>总支出</small><i class="fa fa-level-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'dataColumnClass' => \common\widgets\DataColumn::className(),
                        'columns' => [
                            [
                                'label'=>'id',
                                'encodeLabel' => false,
                                'attribute'=>'id',
                                'format'=>'html',
                                'value'=>function($model) {
                                    return $model->id;
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'id', ['class'=>'form-control']),
//                                'headerOptions'=>['class'=>'footable-visible footable-sortable footable-first-column'],
//                                'contentOptions'=>['class'=>'footable-visible footable-first-column'],
                                'enableSorting' => false, //客户端分页
                                'footer' => '
                                    <td colspan="11">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',          //前端分页
                            ],
                            [
                                'label'=>'时间',
                                'encodeLabel' => false,
                                'attribute'=>'created_time',
                                'format'=>'html',
                                'value'=>function($model) {
                                    return $model->created_time;
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'created_time', ['class'=>'form-control']),
//                                'headerOptions'=>['class'=>'footable-visible footable-sortable', 'data-hide'=>'phone'],
//                                'contentOptions'=>['class'=>'footable-visible'],
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'摘要',
                                'encodeLabel' => false,
                                'attribute'=>'note',
                                'format'=>'html',
                                'value'=>function($model) {
                                    if(strlen($model->note) == 0) {
                                        return '我们将在1-2个工作日内为您提现';
                                    }
                                    return $model->note;
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'note', ['class'=>'form-control']),
                                'headerOptions'=>['data-hide'=>'all'],
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'收入',
                                'encodeLabel' => false,
                                'attribute'=>'cashIn',
                                'format'=>'html',
                                'value'=>function($model) {
                                    $result = $model->cash > 0 ? $model->cash : 0;
                                    return \common\helper\NumberHelper::price_format($result);
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'cashIn', ['class'=>'form-control']),
                                'headerOptions'=>['data-hide'=>'phone'],
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'支出',
                                'encodeLabel' => false,
                                'attribute'=>'cashOut',
                                'format'=>'html',
                                'value'=>function($model) {
                                    $result = $model->cash < 0 ? $model->cash : 0;
                                    return \common\helper\NumberHelper::price_format($result);
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'cashOut', ['class'=>'form-control']),
                                'headerOptions'=>['data-hide'=>'phone'],
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'状态',
                                'encodeLabel' => false,
                                'attribute'=>'status',
                                'format'=>'html',
                                'value'=>function($model) {
                                    if($model->cash > 0) {
                                        return '操作成功';
                                    }
                                    else {
                                        //未汇款的话
                                        if($model->pay_time == 0) {
                                            return '未汇款';
                                        }
                                        else {
                                            return '已汇款';
                                        }
                                    }
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'status', ['class'=>'form-control']),
                                'headerOptions'=>['data-hide'=>'phone'],
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'余额',
                                'encodeLabel' => false,
                                'attribute'=>'balance',
                                'format'=>'html',
                                'value'=>function($model) {
                                    return $model->balance;
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'balance', ['class'=>'form-control']),
                                'headerOptions'=>['data-hide'=>'phone'],
                                'enableSorting' => false, //客户端分页
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div></div>
