<?php

use brand\models\CashRecord;
use common\helper\DateTimeHelper;
use common\helper\NumberHelper;

/* @var $this yii\web\View */
/* @var $searchModel brand\models\CashRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收支对账';
$this->params['breadcrumbs'][] = $this->title;

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/statement/statement.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/grid.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/supplier/statement.js?version='.$r_version.'"></script>
<script>steel.boot(\'app/supplier/statement\');</script>;';
?>
<div class="row animated fadeInRight">
    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">当前可提现</span>
                <h5>账户余额</h5>
            </div>
            <div class="ibox-content">
                <h2 class="no-margins"><?=NumberHelper::format_as_money($total_cash)?></h2>
                <div class="stat-percent font-bold text-info">
                    <button type="button" class="btn btn-pink btn-xs" xm-action="withDraw" data-toggle="modal" data-target="#myModal">立即提现</button>
                </div>
                <small>交易完成后的货款</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">订单完成后可提现</span>
                <h5>交易中</h5>
            </div>
            <div class="ibox-content">
                <h2 class="no-margins text-danger"><?=NumberHelper::format_as_money($total_frozen)?></h2>
                <small>进行中订单的货款金额</small><i class="fa fa-level-up"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right"></span>
                <h5>已提现</h5>
            </div>
            <div class="ibox-content">
                <h2 class="no-margins text-danger"><?=NumberHelper::format_as_money($total_out_cash)?></h2>
                <small>进行中订单的货款金额</small><i class="fa fa-level-up"></i>
            </div>
        </div>
    </div>


</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form action="/index.php" method="get">
                <input type="hidden" name="r" value="cash-record/index">
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="date_added">开始时间</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="start_date" id="date_added" type="text" class="form-control" value="<?=$queryParams['start_date']?>">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="date_modified">结束时间</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input id="date_modified" type="text" class="form-control" name="end_date"  value="<?=$queryParams['end_date']?>">
                    </div>
                </div>
            </div>
            <div class="radio radio-success radio-inline" style="margin-top: 25px;">
                <a href="/index.php?r=cash-record/index">
                <input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
                <label for="inlineRadio1"> 全部流水 </label>
                </a>
            </div>
            <div class="radio radio-success radio-inline" style="margin-top: 25px;">
                <a href="/index.php?r=cash-record/index&cash_status=<?=CashRecord::CASH_RECORD_TYPE_IN?>">
                <input type="radio" id="inlineRadio2" value="option2" name="radioInline">
                <label for="inlineRadio2"> 收入记录 </label>
                </a>
            </div>
            <div class="radio radio-success radio-inline" style="margin: 25px 10px 0 0 ;">
                <a href="/index.php?r=cash-record/index&cash_status=<?=CashRecord::CASH_RECORD_TYPE_OUT?>">
                <input type="radio" id="inlineRadio3" value="option3" name="radioInline">
                <label for="inlineRadio3"> 支出记录 </label>
                </a>
            </div>
            <button type="submit" style="margin-top: 22px;" class="btn btn-w-m btn-primary ">确定</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th data-hide="phone">时间</th>
                            <th data-hide="phone" data-sort-ignore="true">摘要</th>
                            <th data-hide="phone">收入</th>
                            <th data-hide="phone,tablet" >支出</th>
                            <th data-hide="phone,tablet" >状态</th>
                            <th data-hide="phone,tablet" >余额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($record_list as $record) : ?>
                        <tr>
                            <td>
                                <?=$record->id?>
                            </td>
                            <td>
                                <?=DateTimeHelper::getFormatCNDateTime($record->created_time)?>
                            </td>
                            <td>
                                <?php
                                    if (!$record->note) {
                                        echo '我们将在1-2个工作日内为您提现';
                                    } else {
                                        if (strtotime($record->pay_time) > 0) {
                                            echo $record->note;
                                        } else {
                                            echo $record->note;
                                        }
                                    }
                                ?>

                            </td>
                            <td>
                                <?=($record->cash > 0) ? NumberHelper::format_as_money($record->cash) : NumberHelper::format_as_money(0)?>
                            </td>
                            <td>
                                <?=($record->cash < 0) ? NumberHelper::format_as_money(abs($record->cash)) : NumberHelper::format_as_money(0)?>
                            </td>
                            <td>
                                操作成功
                            </td>
                            <td>
                                <?=$record->balance?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">提现申请</h4>
                <small class="font-bold"></small>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>转入账号:</label>
                    <p><?= Yii::$app->user->identity->bankinfo['bank_name'] ?> <?= Yii::$app->user->identity->bankinfo['bank_card_no'] ?> 储蓄卡</p>
                </div>
                <div class="form-group">
                    <label>请输入提现金额</label>
                    <input type="text" placeholder="金额最大不超过可提取余额" class="form-control" name="cash">
                    <small>可提取余额：<?= $total_cash ?></small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-pink" xm-node="withDraw">确认提现</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->