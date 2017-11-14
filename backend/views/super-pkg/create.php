<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SuperPkg */

$this->title = '新建礼包';
$this->params['breadcrumbs'][] = ['label' => 'Super Pkgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="super-pkg-create">


    <?= $this->render('_form', [
        'model' => $model,
        'giftPkgList' => $giftPkgList,
    ]) ?>

</div>
