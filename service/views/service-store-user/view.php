<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use service\assets\UserStoreDetailAsset;
UserStoreDetailAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '门店详情';
$this->params['breadcrumbs'][] = ['label' => '门店管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['steel_boot'] = 'app/service/storeDetail';
?>
<div class="row">

</div>

<div class="row">
    <div class="col-lg-4 store_name">
        <div class="widget lazur-bg p-xl">
            <h2>
                <?= $model->company_name ?>
            </h2>
            <ul class="list-unstyled m-t-md">
                <li>
                    <span class="fa fa-envelope m-r-xs"></span>
                    <label>用户名:</label>
                    <?= $model->getUserShowName()?>
                </li>
                <li>
                    <span class="fa fa-home m-r-xs"></span>
                    <label>门店地址:</label>
                    <?= $model->provinceRegion['region_name'].$model->cityRegion['region_name'].' '.$model->address['address'] ?>
                </li>
                <li>
                    <span class="fa fa-phone m-r-xs"></span>
                    <label>联系电话:</label>
                    <?= $model->mobile_phone ?>
                </li>
            </ul>
        </div>

    </div>

    <div class="col-lg-6 sort_goods">
        <?php if(count($top3) == 3):?>
        <div class="ibox-content">
            <div>
                <?php $index=1; foreach($top3 as $top):?>
                    <div>
                        <span>
                            <button type="button" class="btn <?php echo Yii::$app->params['top3_cs_style'][$index]?> m-r-sm sort_num"><?php echo $index;?></button>
                            <?= $top->goods_name?>
                        </span>
                        <small class="pull-right">计<?=$top->number?>支 占总销售数量<?= ceil(($top->number/$allgoods->number)*100)?>%</small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= ceil(($top->number/$allgoods->number)*100)?>%;" class="progress-bar progress-bar-<?php echo Yii::$app->params['top3_pro_cs'][$index] ?>"></div>
                    </div>
                    <?php $index++;?>
                <?php endforeach;?>
            </div>
        </div>

        <?php else:?>
            <div class="ibox-content navy-bg" >
                <div class="noData">
                    <div class="col-xs-12 text-center">
                        <i class="fa fa-cloud fa-5x" style="color:#fff;" ></i>
                    </div>
                    <span> 该门店暂无商品销售数的前三排行 </span>
                </div>
            </div>
        <?php  endif;?>
    </div>

    <div class="col-lg-2">
        <div class="widget style1 red-bg price_item">
            <div class="row">
                <div class="col-xs-4">
                    <i class="fa fa-thumbs-up fa-3x"></i>
                </div>
                <div class="col-xs-8 text-right">
                    <span> 累计采购金额 </span>
                    <h3 class="font-bold">￥ <?php
                        $divideTotal=0;
                        foreach($model->orderGroups as $orderGroup)
                        {
                            $divideTotal += $orderGroup->alreadyDivide;
                        }
                        ?>
                        <?= \common\helper\NumberHelper::price_format($totalAcount->total_amount-$totalAcount->total_discount)?></h3>
                </div>
            </div>
        </div>
        <div class="widget style1 yellow-bg profits price_item">
            <div class="row">
                <div class="col-xs-4">
                    <i class="fa fa-shield fa-3x"></i>
                </div>
                <div class="col-xs-8 text-right">
                    <span> 累计贡献分成 </span>
                    <h3 class="font-bold">￥ <?= \common\helper\NumberHelper::price_format($divideTotal) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <?php echo $this->render('_searchForStore', [
        'model' => $searchModel,
    ]); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <?= \common\widgets\GridView::widget([
                'showFooter' => true,                    //使用前端分页 shiningxiao
                'dataProvider' => $dataProvider,
                'dataColumnClass' => \common\widgets\DataColumn::className(),
                'columns' => [
                    [
                        'label'=>'订单号',
                        'encodeLabel' => false,
                        'attribute'=>'group_id',
                        'format'=>'html',
                        'value'=>function($model) {
                            return $model->group_id;
                        },
                        'filter'=>Html::activeTextInput($searchModel, 'group_id', ['class'=>'form-control']),
                        'footer' => '
                                    <td colspan="11">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'label'=>'下单时间',
                        'encodeLabel' => false,
                        'attribute'=>'create_time',
                        'value'=>function($model) {
                            return \common\helper\DateTimeHelper::getFormatCNDateTime($model->create_time);
                        },
                       // 'filter'=>Html::activeTextInput($searchModel, 'create_time', ['class'=>'form-control']),
                        'headerOptions'=>['data-hide'=>'phone'],
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'label'=>'订单金额',
                        'encodeLabel' => false,
                        'attribute'=>'goods_amount',
                        //'filter'=>Html::activeTextInput($searchModel, 'consignee', ['class'=>'form-control']),
                        'headerOptions'=>['data-hide'=>'phone'],
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'label'=>'收货人',
                        'encodeLabel' => false,
                        'attribute'=>'consignee',
                       // 'filter'=>Html::activeTextInput($searchModel, 'consignee', ['class'=>'form-control']),
                        'headerOptions'=>['data-hide'=>'phone'],
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'label'=>'联系方式',
                        'encodeLabel' => false,
                        'attribute'=>'mobile',
                        //'filter'=>Html::activeTextInput($searchModel, 'mobile', ['class'=>'form-control']),
                        'headerOptions'=>['data-hide'=>'phone'],
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'label'=>'订单状态',
                        'encodeLabel' => false,
                        'attribute'=>'group_status',
                        'format' => 'raw',
                        'value'=>function($model) {
                            return \common\models\OrderGroup::$group_status_cs_map[$model->group_status];;
                        },
                        'filter'=>Html::activeTextInput($searchModel, 'group_status', ['class'=>'form-control']),
                        'headerOptions'=>['data-hide'=>'phone'],
                        'enableSorting' => false, //客户端分页
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return '<div class="btn-group" >'. Html::a(
                                    '订单详情',
                                    '/service-order-group/view?id='.$model->id,
                                    [
                                        'class' => 'btn btn-outline btn-primary',
                                    ]
                                ).'</div>';
                            },
                        ],
                    ],
                ],
                ]);?>
            </div>
        </div>
    </div>
</div>

