<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexStarGoodsConf */

$this->title = '新建首页楼层商品配置';
$this->params['breadcrumbs'][] = ['label' => '首页楼层商品配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-goods-conf-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'allGoods' => $allGoods,
        'allTabs' => $allTabs,
    ]) ?>

</div>
