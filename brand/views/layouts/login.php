<?php

use yii\helpers\Html;

?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?=isset($this->params['ext_css']) ? $this->params['ext_css'] : ''?>
    <script>
        $CONFIG = {};
        $CONFIG['resPath']='http://adminjs.xiaomei360.com/';
        $CONFIG['version']=<?=\Yii::$app->params['r_version']?>;
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?3984549160280a2786b016453d021525";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <style>input:-webkit-autofill { -webkit-box-shadow: 0 0 0px 1000px #1aa78b inset;}</style>
</head>

<?= $content ?>

<?=isset($this->params['ext_js']) ? $this->params['ext_js'] : ''?>

</html>
