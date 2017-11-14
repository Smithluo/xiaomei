<?php

\service\assets\LoginAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= \yii\helpers\Html::csrfMetaTags() ?>
    <title>小美诚品 | 服务商登录</title>
    <script>
        $CONFIG = {};
        $CONFIG['resPath']='http://adminjs.xiaomei360.com/';
        $CONFIG['version']='<?= Yii::$app->params['r_version'] ?>';
    </script>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<div class="main">
    <div class="header">
        <div class="header-content">
            <img src="http://adminjs.xiaomei360.com/img/login/logo_red.png">
            <span>服务商登录</span>
        </div>
    </div>
    <div class="bg1">
        <img src="http://adminjs.xiaomei360.com/img/login/bg1.png">
    </div>
    <div class="form-zoom animated fadeInDown">
        <div class="welcome">欢迎回来</div>
        <?= $content ?>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<script>steel.boot('<?= $this->params['steel_boot'] ?>');</script>
</html>
<?php $this->endPage() ?>

