<?php

use kartik\helpers\Html;
use common\helper\ImageHelper;
use yii\helpers\Url;
use common\helper\NumberHelper;

?>

<div class="row">
<?php foreach ($topGoods as $index => $goods): ?>

    <div class="col-lg-2">
        <pre style="height:170px;">top<?= $index + 1 ?> <?= Html::a($goods['goods']['goods_name'], 'http://www.xiaomei360.com/goods.php?id='. $goods['goods']['goods_id'], [
                'target' => '_blank',
                'style' => 'white-space:pre-wrap'
            ]) ?>

主图：<?= Html::img(ImageHelper::get_image_path($goods['goods']['goods_thumb']), [
    'style' => 'width: 50px;height: 50px',
            ]) ?>

商品货号：<?= Html::a($goods['goods']['goods_sn'], Url::to([
                '/goods/view', 'id' => $goods['goods']['goods_id'],
            ]), [
                'target' => '_blank',
            ]) ?>

累计采购：<?= $goods['number'] ?>

采购数量占比：<?= NumberHelper::discount_format(100 * $goods['number'] / $totalNum) ?>%</pre>
    </div>

<?php endforeach; ?>
</div>
