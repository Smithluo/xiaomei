<?php
\service\assets\HomeAsset::register($this);
$this->title = '首页';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/mainIndex';

?>
<div class="row animated fadeInRight">
    <?php if (Yii::$app->user->can('service_boss')): ?>
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>返点佣金池</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-10">
                        <h2 class="no-margins text-navy">￥ <?= $this->context->divideAll ?></h2>
                        <small style="margin-top: 10px; display: block;">小美诚品提供给服务商存放订单佣金的工具，该金额为未被提取至钱包的佣金总额。</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">全部可提取</span>
                <h5>钱包</h5>
            </div>
            <div class="ibox-content">
                <h2 class="no-margins">￥ <?= $this->context->cashAll ?></h2>
                <div class="stat-percent font-bold text-info">
                    <button type="button" class="btn btn-pink btn-xs" xm-action="withDraw" data-toggle="modal" data-target="#myModal">立即提现</button>
                </div>
                <small style="margin-top: 10px; display: block;">小美诚品给服务商提供的佣金提现专用工具，钱包中的金额为服务商已完成订单的佣金可以提现的总额。</small>
            </div>

        </div>
    </div>

    <?php endif; ?>

</div>

<div class="row animated fadeInRight">
    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>企业信息</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content ibox-heading" style="padding-bottom:0px;">
                <h3><i class="fa fa-joomla"></i> <?= Yii::$app->user->identity['company_name'] ?></h3>
                <small><i class="fa fa-tim"></i> </small>
            </div>
            <div class="ibox-content">
                <table class="table table-striped" style="margin-top: -10px;">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td> <i class="fa fa-briefcase"></i> &nbsp;用户ID</td>
                        <td><?= Yii::$app->user->identity['user_id'] ?></td>
                    </tr>
                    <tr>
                        <td> <i class="fa fa-cc-visa"></i> 用户级别</td>
                        <td>小美诚品服务商</td>
                    </tr>
                    <tr>
                        <td> <i class="fa fa-dashboard"></i> &nbsp;注册时间</td>
                        <td><?= Yii::$app->formatter->asDate(Yii::$app->user->identity['reg_time'], 'yyyy-MM-dd') ?></td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-institution"></i> &nbsp;公司名称</td>
                        <td><?= Yii::$app->user->identity['company_name'] ?></td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-graduation-cap"></i> &nbsp;联系人</td>
                        <td><?= Yii::$app->user->identity['user_name'] ?></td>
                    </tr>
                    <tr>
                        <td> <i class="fa fa-tty"></i> &nbsp;联系电话</td>
                        <td><?= Yii::$app->user->identity['mobile_phone'] ?></td>
                    </tr>
                    <?php if (Yii::$app->user->can('service_boss')): ?>
                    <tr>
                        <td> <i class="fa fa-cc-visa"></i> 转账卡号</td>
                        <td><?php
                            if(Yii::$app->user->identity->bank_info_id > 0) {
                                echo Yii::$app->user->identity->bankinfo->bank_card_no;
                            }
                            else {
                                echo '未绑定银行账户';
                            }
                            ?></td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (Yii::$app->user->can('service_boss')): ?>
    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>业务员</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-hover no-margins">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>邀请码ID</th>
                        <th>手机号</th>
                        <th>提成余额</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($servicers as $servicer) :
                        ?>
                        <tr>
                            <td><small><?= $servicer->nickname ?></small></td>
                            <td> <?= $servicer->servicer_code ?></td>
                            <td><?= $servicer->mobile_phone ?></td>
                            <td class="text-navy">￥<?= \common\helper\NumberHelper::price_format($servicer->divide_amount) ?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
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
                    <small>可提取余额：￥<?= $this->context->cashAll ?></small>
                    <?php // $this->context->cashAll?>
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


