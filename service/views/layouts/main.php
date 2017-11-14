<?php

use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
\service\assets\BaseAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <script>
        $CONFIG = {};
        $CONFIG['resPath']='http://adminjs.xiaomei360.com/';
        $CONFIG['version']='<?= Yii::$app->params['r_version'] ?>';
    </script>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="ecommerce-orders.html#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold"><?= Yii::$app->user->identity['company_name'] ?></strong>
                                </span>
                            </span>
                            <?php if (Yii::$app->user->can('service_boss')): ?>
                            <h4 class="font-extra-bold m-b-xs">
                                <span class="text-muted text-xs block">返点佣金池：</span>
                                ￥<?= $this->context->divideAll ?> <b class="caret"></b>
                            </h4>
                            <?php endif; ?>
                        </a>
                        <?php if (Yii::$app->user->can('/service-cash-record/index')): ?>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="<?= \yii\helpers\Url::to(['/service-cash-record/index']) ?>">钱包余额：￥<?= $this->context->cashAll ?></a></li>
                            <li class="divider"></li>
                            <li><a href="<?= \yii\helpers\Url::to(['/service-cash-record/index']) ?>">查看对账单</a></li>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div class="logo-element">
                        <img alt="image" style="height:50px;width:50px;border-radius:0" class="img-circle" src="http://m.xiaomei360.com/data/attached/cat_image/32e3ffc876047072242fdf76e9b27770.png" />
                    </div>
                </li>
                <?php if (Yii::$app->user->can('/service-site/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-site' && $this->context->action->id == 'index') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="/">
                        <i class="fa fa-diamond"></i>
                        <span class="nav-label">首页</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-order-group/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-order-group') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="<?= \yii\helpers\Url::to(['/service-order-group/index']) ?>">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="nav-label">订单列表</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-servicer-user/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-servicer-user') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="<?= \yii\helpers\Url::to(['/service-servicer-user/index']) ?>">
                        <i class="fa fa-vimeo-square"></i>
                        <span class="nav-label">人员管理</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-store-user/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-store-user') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="
                    <?=
                    //如果是服务商老板就可以看到待审核列表，否则只能看到绑定用户的列表
                    \yii\helpers\Url::to(['/service-store-user/index', 'is_checked' => Yii::$app->user->can('service_boss')?0:2])
                    ?>
                    ">
                        <i class="fa fa-cubes"></i>
                        <span class="nav-label">门店管理</span>
                    </a>
                    <ul class="nav nav-second-level collapse">

                    <?php if (Yii::$app->user->can('service_boss') || Yii::$app->user->can('service_manager') ): ?>
                        <li <?php
                        if($this->context->id == 'service-store-user' && empty($_GET['is_checked'])) {
                            echo 'class="active"';
                        }
                        ?>>
                            <a href="<?= \yii\helpers\Url::to(['/service-store-user/index', 'is_checked' => 0]) ?>">待审核列表</a>
                        </li>
                    <?php endif; ?>

                        <li <?php
                        if($this->context->id == 'service-store-user' &&
                            (!empty($_GET['is_checked'])  || !empty($_GET['UsersSearch[is_checked]']))
                        ) {
                            echo 'class="active"';
                        }
                        ?>>
                            <a href="<?= \yii\helpers\Url::to(['/service-store-user/index', 'is_checked' => 2]) ?>">门店列表</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-brand/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-brand') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="<?= \yii\helpers\Url::to(['/service-brand/index']) ?>">
                        <i class="fa fa-tasks"></i>
                        <span class="nav-label">分成管理</span>
                        <span class="pull-right" style="color: #f15b82;"><i class="fa fa-star"></i></span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-cash-record/index')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-cash-record') {
                        echo 'class="active"';
                    }
                    ?>
                >
                    <a href="<?= \yii\helpers\Url::to(['/service-cash-record/index']) ?>">
                        <i class="fa fa-files-o"></i>
                        <span class="nav-label">收支对账</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Yii::$app->user->can('/service-site/change-password')): ?>
                <li
                    <?php
                    if($this->context->id == 'service-site' && $this->context->action->id == 'change-password') {
                        echo 'class="active"';
                    }
                    ?>
                    >
                    <a href="#">
                        <i class="fa fa-magic"></i>
                        <span class="nav-label">账户管理</span>
                    </a>
                    <ul <?php
                        if($this->context->id == 'service-site' && $this->context->action->id == 'change-password') {
                            echo 'class="nav nav-second-level collapse in"';
                        }
                        else {
                            echo 'class="nav nav-second-level collapse"';
                        }
                    ?>>
                        <li <?php
                        if($this->context->id == 'service-site' && $this->context->action->id == 'change-password') {
                            echo 'class="active"';
                        }
                        ?>><a href="<?= \yii\helpers\Url::to(['/service-site/change-password']) ?>">修改密码</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="landing_link">
                    <a target="_blank" href="http://www.xiaomei360.com"><i class="fa fa-star"></i> <span class="nav-label">前往小美商城</span> </a>
                </li>
            </ul>

        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg"> <!--  style="min-height: 953px;" -->
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-pink " href="#"><i class="fa fa-bars"></i> </a>
                    <form role="search" class="navbar-form-custom" action="search_results.html">
                    </form>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">欢迎来到小美诚品 服务商管理系统</span>
                    </li>

                    <?php if (Yii::$app->user->can('service_boss')): ?>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-envelope"></i>  <span class="label label-primary"><?= $this->context->orderDoneNum ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="javascritp:;">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> 您有 <?= $this->context->orderDoneNum ?> 个可提取订单
<!--                                        <span class="pull-right text-muted small">3 小时 以前</span>-->
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="ecommerce-orders.html#">
                            <i class="fa fa-bell"></i>  <span class="label label-danger"><?= $this->context->orderReturnNum ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="javascritp:;">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> 您有 <?= $this->context->orderReturnNum ?> 个退货订 单
<!--                                        <span class="pull-right text-muted small">3 小时 以前</span>-->
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif ?>
                    <li>
                        <?php
                            if (Yii::$app->user->isGuest) {
                                echo '<a href="'. \yii\helpers\Url::to(['/service-site/login']). '">
                                        <i class="fa fa-sign-out"></i> Login
                                    </a>';
                            } else {
                                echo '<a href="'. \yii\helpers\Url::to(['/service-site/logout']). '">
                                        <i class="fa fa-sign-out"></i> Logout('. Yii::$app->user->identity->getUserShowName() .')
                                    </a>';
                            }
                        ?>
                    </li>
                </ul>

            </nav>
        </div>

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2></h2>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            </div>
        </div>


        <?php
        echo $content;
        ?>

        <div class="footer fixed">
            <div class="pull-right">
            </div>
            <div>
                <strong>Copyright</strong> 小美诚品 &copy; 2016-<?= date('Y', time())?>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
<?php if (!empty($this->params['steel_boot'])): ?>
<script>steel.boot('<?= $this->params['steel_boot'] ?>');</script>
<?php endif; ?>
</body>

</html>
<?php $this->endPage() ?>
