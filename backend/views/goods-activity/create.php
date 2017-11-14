<?php

use yii\helpers\Html;
use common\models\GoodsActivity;
use common\models\Goods;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsActivity */

$this->title = '创建 '.$act_type_map[$model->act_type].' 活动';
$this->params['breadcrumbs'][] = ['label' => $act_type_map[$model->act_type], 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//  设置默认值
$model->is_hot = GoodsActivity::IS_HOT_NO;
$model->product_id = 0;
?>
<div class="goods-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'shippingCodeNameMap' => $shippingCodeNameMap,
        'allGoodsList' => $allGoodsList,
        'act_type_map' => $act_type_map,
    ]) ?>

</div>
