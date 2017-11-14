<?php

\backend\assets\BaseAsset::register($this);

?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>小美诚品 | 订单详情</title>

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
    <?php
    echo $content;
    ?>
</div>

<?php $this->endBody() ?>
<script>steel.boot('app/service/superManage');</script>
</body>

</html>
<?php $this->endPage() ?>

