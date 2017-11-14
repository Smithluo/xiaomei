<?php
\service\assets\UserIdentifyAsset::register($this);


$this->title = '用户审核';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/authentication';
?>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $model->company_name?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content ibox-heading" style="padding-bottom:0px;position:relative;">
                            <div class="comName" xm-node="editorText">
                                <h3><i class="fa fa-joomla"></i> <?= $model->company_name?></h3>
                                <small><i class="fa fa-tim"></i> </small>
                            </div>
                            <div class="col-sm-10 editorInput" xm-node="editorInput">
                                <input type="text" class="form-control" value="<?= $model->company_name?>">
                            </div>
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
                                    <td> <i class="fa fa-institution"></i> &nbsp;联系地址</td>
                                    <td><?= $address ?></td>
                                </tr>
                                <tr>
                                    <td> <i class="fa fa-user"></i> &nbsp;联系人姓名</td>
                                    <td><?= $model->getUserShowName() ?></td>
                                </tr>
                                <tr>
                                    <td> <i class="fa fa-vimeo-square"></i> &nbsp;联系人职务</td>
                                    <td><?= !empty($model->extension->duty) ? $model->extension->duty: '' ?></td>
                                </tr>
                                <tr>
                                    <td> <i class="fa fa-tty"></i> &nbsp;手机号码</td>
                                    <td><?= $model->mobile_phone?></td>
                                </tr>
                                <tr>
                                    <td> <i class="fa fa-delicious"></i> &nbsp;店铺数量</td>
                                    <td><?= !empty($model->extension['store_number']) ? $model->extension['store_number'] : '' ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-rmb"></i> &nbsp;月营业额</td>
                                    <td><?= !empty($model->extension->month_sale_count) ? $model->extension->month_sale_count : '' ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-anchor"></i> &nbsp;进口品占比</td>
                                    <td><?= !empty($model->extension->imports_per) ? $model->extension->imports_per : '' ?> </td>
                                </tr>

                                <tr>
                                    <td> <i class="fa fa-stack-exchange"></i> &nbsp;了解小美诚品的渠道</td>
                                    <td><?= !empty($model->channel)  ? $model->channel : '' ?></td>
                                </tr>
                                <tr>
                                    <td> <i class="fa fa-qq"></i> &nbsp;qq号码</td>
                                    <td><?= $model->qq?></td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="ibox-content ibox-heading authImg">
                                <div class="imgItem">
                                    <h4>门头照片:</h4>
                                    <img src="<?= $model->shopfront_pic?>">
                                </div>
                                <div class="imgItem">
                                    <h4>营业执照:</h4>
                                    <img src="<?= $model->biz_license_pic?>">
                                </div>
                            </div>
                            <div class="optionArea">
                                <?php if(!empty($model->extension)) {?>
                                <button type="button" class="btn btn-w-m btn-danger" xm-node="submitReply">通过审核</button>
                                <?php } else{ ?>
                                <button type="button" class="btn btn-w-m default" xm-node="unableReply">通过审核</button>
                                <?php }?>
                                <button type="button" class="btn btn-w-m btn-warning" xm-node = "refuseReply">驳回审核</button>
                            </div>
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
                <h4 class="modal-title">通过申请</h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>门店名称</label>
                    <input type="text" placeholder="请输入门店名称" value="<?= $model->company_name?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>分配业务员</label>
                    <select type="tel" value="请选择" class="form-control">

                        <?php if(!empty($servicerUsers)): foreach($servicerUsers as $servicerUser):?>
                            <option value="<?= $servicerUser->user_id?>"><?= $servicerUser->nickname?></option>
                        <?php endforeach; endif;?>

                    </select>
                </div>
                <div class="form-group">
                    <label>备注</label>
                    <textarea placeholder="请输入备注" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-pink" id="passConfim" xm-data="<?= $model->user_id?>">确认通过</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
<!-- 模态框（Modal） -->
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">驳回申请</h4>
                <small class="font-bold"></small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>驳回原因</label>
                    <textarea placeholder="请输入备注" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-pink" id="refusedConfim" xm-data="<?= $model->user_id?>">确认驳回</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->




