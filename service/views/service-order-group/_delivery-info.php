<div class="logis_item">
    <div class="logis_info">
        <div class="logis_imgs">
            <?php foreach ($deliveryItem['goods_list'] as $goods): ?>
            <img src = "<?= $goods['goods_thumb'] ?>">
            <?php endforeach; ?>
        </div>
        <div class="logis_progress">
            <?php foreach ($deliveryItem['shipping_info']['result']['list'] as $shippingInfo): ?>
            <p><?= $shippingInfo['time'] ?>   <?= $shippingInfo['status'] ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</div>