<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\BrandUser;
use brand\models\OrderInfo;
use brand\models\CashRecord;
use common\helper\NumberHelper;
use brand\models\BrandDivideRecord;

$start_time = BrandUser::find()->where(['user_id' => Yii::$app->user->identity->getId()])->one()->reg_time;
$to_be_shipped = OrderInfo::getToBeOrders(OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED, $start_time);
$to_be_shipped_url = '/index.php?r=order&order_cs_status='.OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED;
$to_be_returned = OrderInfo::getToBeOrders(OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED, $start_time);
$to_be_returned_url = '/index.php?r=order&order_cs_status='.OrderInfo::ORDER_CS_STATUS_RETURNED;
$to_be_show = OrderInfo::getToBeOrders('', $start_time);
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="favicon.ico" rel="shortcut icon">
    <link href="http://adminjs.xiaomei360.com/lib/base.css?version=<?=\Yii::$app->params['r_version']?>" type="text/css" rel="stylesheet">
    <?=isset($this->params['ext_css']) ? $this->params['ext_css'] : ''?>
    <script>
        $CONFIG = {};
        $CONFIG['resPath']='http://adminjs.xiaomei360.com/';
        $CONFIG['version']=<?=Yii::$app->params['r_version']?>;
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?3984549160280a2786b016453d021525";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>

</head>
<body>
<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>

                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="ecommerce-orders.html#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold"><?=BrandUser::getUserInfo('company_name')->company_name?></strong>
                                </span>
                            </span>
                            <h4 class="font-extra-bold m-b-xs">
                                <span class="text-muted text-xs block">账户余额：</span>
                                <?=NumberHelper::format_as_money(CashRecord::totalCash())?> <b class="caret"></b>
                            </h4>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="/index.php?r=cash-record">交易中：<?=NumberHelper::format_as_money(BrandDivideRecord::totalFrozen($start_time))?></a></li>
                            <li><a href="/index.php?r=cash-record">已提现：<?=NumberHelper::format_as_money(CashRecord::totalOutCash())?></a></li>
                            <li class="divider"></li>
                            <li><a href="/index.php?r=cash-record">查看对账单</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        <img alt="image" style="height:50px;width:50px;border-radius:0" class="img-circle" src="http://m.xiaomei360.com/data/attached/cat_image/32e3ffc876047072242fdf76e9b27770.png" />
                    </div>
                </li>
                <li <?php if ($_GET['r'] == 'order'): ?>class="active"<?php endif; ?>>
                    <a href="/index.php?r=order#">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="nav-label">订单管理</span>
                        <span class="label label-info pull-right"><?=$to_be_show['count']?></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li <?php if ($_GET['r'] == 'order' && (!isset($_GET['order_cs_status']))): ?>class="active"<?php endif; ?>><a href="/index.php?r=order&a=index">订单列表</a></li>
                        <li <?php if ($_GET['r'] == 'order' && (isset($_GET['order_cs_status']) && $_GET['order_cs_status'] == OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED)): ?>class="active"<?php endif; ?>><a href="<?=$to_be_shipped_url?>">待发货订单</a></li>
                        <li <?php if ($_GET['r'] == 'order' && (isset($_GET['order_cs_status']) && $_GET['order_cs_status'] == OrderInfo::ORDER_CS_STATUS_RETURNED)): ?>class="active"<?php endif; ?>><a href="<?=$to_be_returned_url?>">待退货订单</a></li>
                    </ul>
                </li>
                <li <?php if ($_GET['r'] == 'brand'): ?>class="active"<?php endif; ?>>
                    <a href="/index.php?r=brand">
                        <i class="fa fa-diamond"></i>
                        <span class="nav-label">品牌管理</span>
                    </a>
                </li>
                <li <?php if ($_GET['r'] == 'goods'): ?>class="active"<?php endif; ?>>
                    <a href="/index.php?r=goods">
                        <i class="fa fa-sitemap"></i>
                        <span class="nav-label">商品管理</span>
                    </a>
                </li>
                <li <?php if ($_GET['r'] == 'cash-record'): ?>class="active"<?php endif; ?>>
                    <a href="/index.php?r=cash-record">
                        <i class="fa fa-files-o"></i>
                        <span class="nav-label">收支对账</span>
                    </a>
                </li>

                <li <?php if ($_GET['r'] == 'user'): ?>class="active"<?php endif; ?>>
                    <a href="/index.php?r=user#">
                        <i class="fa fa-magic"></i>
                        <span class="nav-label">账户管理</span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li <?php if ($_GET['r'] == 'user' && (isset($_GET['a']) && $_GET['a'] == 'modify-pwd')): ?>class="active"<?php endif; ?>>
                            <a href="/index.php?r=site/change-password">修改密码</a>
                        </li>
                        <li <?php if ($_GET['r'] == 'user' && (!isset($_GET['a']) || $_GET['a'] == 'info')): ?>class="active"<?php endif; ?>>
                            <a href="/index.php?r=user/userinfo">企业信息</a>
                        </li>
                    </ul>
                </li>

                <li class="landing_link">
                    <a target="_blank" href="http://www.xiaomei360.com"><i class="fa fa-star"></i> <span class="nav-label">前往小美商城</span> </a>
                </li>
            </ul>

        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    <form role="search" class="navbar-form-custom" action="search_results.html">

                    </form>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">欢迎来到小美诚品 品牌方管理系统</span>
                    </li>
                    <li class="dropdown">
                        <?php if ($to_be_shipped['count']) : ?>
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-envelope"></i>  <span class="label label-primary"><?=$to_be_shipped['count']?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="<?=$to_be_shipped_url?>">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> 您有 <?=$to_be_shipped['count']?> 个订单待发货
                                        <span class="pull-right text-muted small"><?=$to_be_shipped['last_time']?> 以前</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <?php else: ?>
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li class="dropdown">
                        <?php if ($to_be_returned['count']) : ?>
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-bell"></i>  <span class="label label-danger"><?=$to_be_returned['count']?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="<?=$to_be_returned_url?>">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> 您有 <?=$to_be_returned['count']?> 个退货订单
                                        <span class="pull-right text-muted small"><?=$to_be_returned['last_time']?> 以前</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <?php else: ?>
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-bell"></i>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <a href="/index.php?r=site/logout">
                            <i class="fa fa-sign-out"></i> 退出账号
                        </a>
                    </li>
                </ul>

            </nav>
        </div>

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-9">
                <h2></h2>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>

            </div>

            <?=isset($this->params['get_all_cash']) ? $this->params['get_all_cash'] : ''?>
        </div>

        <?= $content ?>
        <div class="footer">
            <div>
                <strong>Copyright</strong> 小美诚品 &copy; <?=date('Y')?>
            </div>
        </div>
    </div>

</div>
<?=isset($this->params['ext_js']) ? $this->params['ext_js'] : ''?>
</body>
</html>