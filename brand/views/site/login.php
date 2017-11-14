<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = '小美诚品 | 品牌商登录';

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/service/login/login.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/service/login.js?version='.$r_version.'"></script>
<script>steel.boot(\'app/service/login\');</script>';
?>
<body class="pageSize-green">
<div class="main">
    <div class="header">
        <div class="header-content">
            <img src="http://adminjs.xiaomei360.com/img/login/logo-green.png">
            <span style="color:#1aa78b;">品牌方登录</span>
        </div>
    </div>
    <div class="bg1">
        <img src="http://adminjs.xiaomei360.com/img/login/bg1.png">
    </div>
    <div class="form-zoom animated fadeInDown">
        <div class="welcome">欢迎回来</div>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <div class="form-group">
            <i class="uname"></i>
            <input type="text" id="brandloginform-username" class="form-control  input-green" name="BrandLoginForm[username]" autofocus="" placeholder="请输入账号名" autocomplete="off">
            <div class="help-block"></div>
        </div>

        <div class="form-group field-brandloginform-password required">
            <i class="psd"></i>
            <input type="password" id="brandloginform-password" class="form-control input-green" placeholder="请输入密码"  name="BrandLoginForm[password]">
            <div class="help-block"></div>
        </div>

        <div class="txt">
            <label class="tips"><?=$code ? $err_msg : ''?></label>
            <button type="submit" class="btn btn-green">登录</button>
        </div>


        <?php ActiveForm::end(); ?>
    </div>
</div>
</body>

