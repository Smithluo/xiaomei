<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;
use common\models\Region;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = $model->company_name;
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = '企业信息';

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/modifyPsd/modifyPsd.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>';
?>
<div class="users-view">
    <div class="row">
        <div class="col-lg-10">
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
                    <h3><i class="fa fa-joomla"></i> <?=Html::encode($this->title)?></h3>
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
                            <td> <i class="fa fa-briefcase"></i> &nbsp;账户ID</td>
                            <td><?=$model->user_id?></td>
                        </tr>
                        <tr>
                            <td> <i class="fa fa-cc-visa"></i> 用户级别</td>
                            <td>品牌商</td>
                        </tr>
                        <tr>
                            <td> <i class="fa fa-dashboard"></i> &nbsp;注册时间</td>
                            <td><?=DateTimeHelper::getFormatCNDateTime($model->reg_time)?></td>
                        </tr>
                        <?php
                            if ($model->address) :
                        ?>
                        <tr>
                            <td><i class="fa fa-institution"></i> &nbsp;公司地址</td>
                            <td><?=Region::getUserAddress($model->address).' '.$model->address->address?></td>
                        </tr>
                        <?php
                            endif;
                        ?>
                        <tr>
                            <td><i class="fa fa-graduation-cap"></i> &nbsp;联系人</td>
                            <td><?=isset($brand_admin) ? $brand_admin->linkman : ''?></td>
                        </tr>
                        <tr>
                            <td> <i class="fa fa-tty"></i> &nbsp;联系电话</td>
                            <td><?=isset($brand_admin) ? $brand_admin->mobile : ''?></td>
                        </tr>
                        <tr>
                            <td> <i class="fa fa-cc-visa"></i> 转账卡号</td>
                            <td><?=isset($bank_info) ? $bank_info->bank_card_no : ''?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
