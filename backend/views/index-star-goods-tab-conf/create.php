<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexStarGoodsTabConf */

$this->title = '新建首页楼层配置';
$this->params['breadcrumbs'][] = ['label' => '首页楼层配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-goods-tab-conf-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
