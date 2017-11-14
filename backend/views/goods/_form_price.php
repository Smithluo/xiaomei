<h3>Tips:不同会员等级的起售数量都应该是起售数量的整数倍；积分兑换商品不设置梯度价格和不同等级的起售数量——简化规则</h3>
<div class="col-lg-4">
    <?= $form->field($model, 'number_per_box')->textInput() ?>

    <?= $form->field($model, 'start_num')->textInput() ?>

    <?= $form->field($model, 'volume_number_1')->textInput() ?>

    <?= $form->field($model, 'volume_number_2')->textInput() ?>

    <?php
        foreach ($allUserRank as $userRank) {
            if ($userRank->rank_id > 1) {
                echo $form->field($model, 'loadMoqs['.$userRank->rank_id.']')->textInput([
                    'placeholder' => $userRank->rank_name,
                ])->label($userRank->rank_name.'的起售数量');
            }
        }
    ?>

    <?= $form->field($model, 'goods_number')->textInput() ?>
</div>

<div class="col-lg-4">
    <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_price')->textInput(['maxlength' => true])->label('第一梯度价格(本店售价)') ?>

    <?= $form->field($model, 'volume_price_1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'volume_price_2')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-lg-4">

</div>
