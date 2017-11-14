<?php

use yii\helpers\Html;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\service\assets\ServicerUserAsset::register($this);

$this->title = '人员管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/userMange';
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <?= Html::button('添加员工', [
        'class' => 'col-lg-2 btn btn-pink',
        'style'=>'margin-bottom:20px',
        'data-toggle'=>'modal',
        'data-target'=>'#myModal',
        'xm-node'=>'addModel']) ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
    <?= GridView::widget([
        'showFooter' => true,                    //使用前端分页 shiningxiao
        'dataProvider' => $dataProvider,
        'dataColumnClass' => \common\widgets\DataColumn::className(),
        'columns' => [
            [
                'label'=>'姓名',
                'encodeLabel' => false,
                'attribute'=>'nickname',
                'format'=>'raw',
                'value'=>function($model) {
                    return $model->nickname;
                },
                'filter'=>Html::activeTextInput($searchModel, 'nickname', ['class'=>'form-control']),
                'footer' => '
                                    <td colspan="5">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'电话',
                'encodeLabel' => false,
                'attribute'=>'mobile_phone',
                'format'=>'raw',
                'value'=>function($model) {
                    return $model->mobile_phone;
                },
                'filter'=>Html::activeTextInput($searchModel, 'mobile_phone', ['class'=>'form-control']),
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'业务码',
                'encodeLabel' => false,
                'attribute'=>'servicer_code',
                'format'=>'raw',
                'value'=>function($model) {
                    if($model->servicer_info_id == 0) {
                        return '';
                    }
                    return $model->servicerUserInfo['servicer_code'];
                },
                'filter'=>Html::activeTextInput($searchModel, 'servicer_code', ['class'=>'form-control']),
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'当前提成余额',
                'encodeLabel' => false,
                'attribute'=>'divide_amount',
                'format'=>'raw',
                'value'=>function($model) {
//                    return \common\helper\ServicerDivideHelper::getTotalDivideAmount([$model->user_id])[0]['total_amount'];
                    return  \common\helper\NumberHelper::price_format(\common\models\CashRecord::totalCash($model->user_id));
                },
                'enableSorting' => false, //客户端分页
            ],
            [
                'label'=>'角色',
                'encodeLabel' => false,
                'format'=>'raw',
                'value'=>function($model) {
                    $role = Yii::$app->authManager->getRolesByUser($model->user_id);
                    if(empty($role))
                    {
                        return '未设置';
                    }elseif(!empty($role['service_saleman']))
                    {
                        return $role['service_saleman']->description;
                    }elseif(!empty($role['service_manager']))
                    {
                        return $role['service_manager']->description;
                    }

                },
                'enableSorting' => false, //客户端分页
            ],
            [
                'class' => common\widgets\ActionColumn::className(),
                'header' => '操作',
//                'template' => '<div class="btn-group">
//                                        {modify}
//                                        {delete}
//                                        </div>
//                                    ',
                'template' => '<div class="btn-group">
                                        {modify}
                                        </div>
                                    ',
                'buttons' => [
                    'modify' => function ($url, $model) {
                        return '<button type="button" class="btn btn-outline btn-danger" xm-action="editor" xm-data="id='. $model->user_id.'&username='. $model->nickname.'&phone='. $model->mobile_phone.'&balance='. \common\helper\NumberHelper::price_format(\common\models\CashRecord::totalCash($model->user_id)).'">修改信息</button>';
                    },
//                    'delete' => function ($url, $model) {
//                        return '<button type="button" class="btn btn-outline btn-default" xm-action="delete" xm-data="id='. $model->user_id. '">删除</button>';
//                    },
                ],
                'headerOptions'=>['class' => 'text-right', 'data-sort-ignore' => 'true'],
                'contentOptions'=>['class' => 'text-right'],
                'footer' => '',
            ],
        ],
    ]); ?>
</div></div></div></div>

<!-- 模态框（Modal） -->
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">添加员工</h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>姓名</label>
                    <input type="text" id="username" placeholder="请输入要添加的员工姓名" class="form-control" name="ServiceUser[nickname]">
                </div>
                <div class="form-group">
                    <label>电话</label>
                    <input type="tel" id="phone" placeholder="请输入要添加的员工电话" class="form-control" name="ServiceUser[mobile_phone]">
                </div>
                <div class="form-group">
                    <label>角色</label>
                    <select name="ServiceUser[role]" id="role" class="form-control">
                        <?php if($notHasManager):?>
                        <option value="manager">业务经理</option>
                        <?php endif;?>
                        <option value="saleman">业务员</option>
                    </select>
                </div>
                <?php if(Yii::$app->user->can('service_boss')):?>
                <div class="form-group" id="balance" >
                    <label>提取金额</label>
                    <input type="tel" id="totalNum" name="totalNum" placeholder="" value="0" class="form-control">
                    <small>该业务员余额:<span style="color:#f15b82">￥88888.00</span></small>
                </div>
                <?php endif;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-pink" xm-node="addUser" xm-data="0">确认添加</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->

