<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '品牌管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = '品牌列表';
if (!isset($_GET['brand_id']) || !is_numeric($_GET['brand_id'])) {
    $brand_id_list = array_keys($brand_list);
    $brand_id = array_shift($brand_id_list);
} else {
    $brand_id = $_GET['brand_id'];
}
$brand = $brand_list[$brand_id];

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/brandMange/brandMange.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/brand.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/plugins/dropzone/dropzone.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/supplier/brandMange.js?version='.$r_version.'"></script>
<script>steel.boot("app/supplier/brandMange");</script>';

$brand_url = 'http://sc.xiaomei360.com/brand.php?id='.$brand['brand_id'];
?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-9">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> 品牌信息</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false"> 品牌介绍</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false"> 资质证明</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                            <fieldset class="form-horizontal">
                                <div class="form-group"><label class="col-sm-2 control-label">发货地址</label>
                                    <div class="col-sm-10" style="margin-top:8px;">
                                        <?=$brand['brand_depot_area']?>
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-2 control-label">品牌描述</label>
                                    <div class="col-sm-10">
                                        <img src="<?=$brand['brand_logo_two']?>" />
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-2 control-label">品牌展示</label>
                                    <div class="col-sm-10" style="margin-top:8px;">
                                        <a target="_blank" href="<?=$brand_url?>">点击前往</a>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-2 control-label">品牌故事</label>
                                    <div class="col-sm-10" style="margin-top:8px;">
                                        <?=$brand['brand_desc_long']?>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </fieldset>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane">

                        <div class="panel-body">
                            <?=$brand['brand_content']?>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane">

                        <div class="panel-body">
                            <?=$brand['brand_qualification']?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top: 38px;" class="col-lg-3 animated fadeInRight">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>已入驻小美诚品的品牌</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content no-padding" style="display: block;">
                    <ul class="list-group">
                        <?php
                            foreach ($brand_list as $key => $brand) :
                        ?>
                        <li class="list-group-item">
                            <a href="/index.php?r=brand&brand_id=<?=$key?>" class="pull-left" style="width:140px;">
                                <img alt="image" style="height: 40px; margin-top: 5px;" class="img-circle" src="<?=$brand['brand_logo_two']?>">
                            </a>
                            <div class="media-body ">
                                <p><?=$brand['brand_name']?></p>
                                <a class="btn btn-xs btn-primary" href="/index.php?r=brand&brand_id=<?=$key?>"><i class="fa fa-paint-brush"></i> 查看品牌信息</a>
                            </div>
                        </li>
                        <?php
                            endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
