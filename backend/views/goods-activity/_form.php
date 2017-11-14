<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\GoodsActivity;
use common\helper\DateTimeHelper;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsActivity */
/* @var $form yii\widgets\ActiveForm */
$act_type_map = GoodsActivity::$act_type_map;
$is_finished_map = GoodsActivity::$is_finished_map;
$is_hot_map = GoodsActivity::$is_hot_map;
$buyByBoxMap = GoodsActivity::$buyByBoxMap;
//  开始时间为30天前 3600 * 24 * 30 = 2592000
$start_date = date('Y-m-d H:i:s', time() - 2592000);
?>
<p style="color: red">
    tips：团采的限购数量是每笔订单的限购数量；秒杀的限购数量是活动时段内可购买的总数量；目前是支付减库存，会出现有用户下单支付不了的情况，秒杀订单会在配置的订单有效时间后自动取消
</p>
<p style="color: red"><strong>
    tips：【约束条件】一个SKU不能同时在生效的团采活动和秒杀活动中个都出现。 团采/秒杀 商品的 起售数量、配送方式 填写准确，团采订单的计算将以团采的配置为准。
    </strong></p>
<p style="color: green"><strong>
        秒杀订单 的订单有效期默认1800秒，当前不要改这个值。订单列表中提醒订单有效期30分钟当前是写死的。秒杀商品尽量配置每个用户购买上限是1个或起售数量。配置小美直发包邮 以外的 运费规则是否生效，待验证
    </strong></p>
<p style="color: red">
    tips：团采的达成数量 是指团采商品的下单量 达到这个数就成团，进度显示100%；秒杀的达成数量是 秒杀的设定库存，秒杀的实际库存是 设定库存 和 商品实际库存的 较小值。
</p>
<p style="color: red"><strong>
        【团采商品不使用会员等级折扣】团采的 起售数量、箱规、是否按箱、销售价格、配送方式 与 商品信息配置一致,团采的 物料配比使用参与团采商品的配置;
    </strong></p>
<div class="goods-activity-form">

    <?php
    if (!$model->isNewRecord) {
        echo Html::a('PC站预览', 'http://www.xiaomei360.com/group_buy_preview.php?id='. $model->act_id, [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);
        echo Html::a('微信站预览', 'http://m.xiaomei360.com/default/groupbuy/preview/id/'. $model->act_id.'.html', [
            'class' => 'btn btn-success',
            'target' => '_blank',
        ]);
    }
    ?>

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-8\">{input}</div>\n
                <div class=\"col-lg-4\"></div>
                <div class=\"col-lg-8\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="form-group">
        <div class="col-lg-4">
            <?= $form->field($model, 'act_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-7">
            <?= $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $allGoodsList,
                'options' => ['placeholder' => '选择参与活动的商品'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>

        <div class="col-lg-1">
            <?= Html::submitButton(
                $model->isNewRecord ? '创建' : '提交',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    </div>
    <div class="row"></div>
    <div class="col-lg-4">
        <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'note')->textInput() ?>

        <?= $form->field($model, 'sort_order')->textInput() ?>

        <?php if ($model->act_type != GoodsActivity::ACT_TYPE_GROUP_BUY) : ?>
        <?= $form->field($model, 'start_num')->textInput() ?>

        <?= $form->field($model, 'act_price')->textInput(['maxlength' => true]) ?>
        <?php endif; ?>

        <?= $form->field($model, 'old_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'match_num')->textInput() ?>

        <?= $form->field($model, 'limit_num')->textInput() ?>
        <p>当前团采的限购数量是每一次订单的最大可购买数量，秒杀的限购数量是这个活动时段内活动商品的购买总数量</p>

        <?= $form->field($model, 'show_banner')->fileInput(['accept' => 'image/*']) ?>
        <?= Html::img($model->getUploadUrl('show_banner'), ['height' => '200']) ?>
    </div>
    <div class="col-lg-4">
        <!--        --><?php //$form->field($model, 'start_time')->textInput() ?>
        <!--        --><?php //$form->field($model, 'end_time')->textInput() ?>
        <!-- 活动开始时间 start -->
        <div class="col-lg-2"></div>
        <div class="col-lg-2">
            <label>
                <?= $model->attributeLabels()['start_time']; ?>
            </label>
        </div>
        <div>
            <?= DateTimePicker::widget([
                'name' => 'GoodsActivity[start_time]',
                'value' => date('Y-m-d H:i:s', DateTimeHelper::getFormatCNTimesTimestamp($model->start_time)),
                'options' => ['placeholder' => DateTimeHelper::getFormatCNDateTime(time())],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'yyyy-MM-dd HH:i:ss',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'startDate' => $start_date,
                ]
            ]);?>
        </div>
        <!-- 活动开始时间 end -->

        <!-- 活动结束时间 start -->
        <div class="col-lg-2"></div>
        <div class="col-lg-2">
            <label>
                <?= $model->attributeLabels()['end_time']; ?>
            </label>
        </div>
        <div>
            <?= DateTimePicker::widget([
                'name' => 'GoodsActivity[end_time]',
                'value' => date('Y-m-d H:i:s', DateTimeHelper::getFormatCNTimesTimestamp($model->end_time)),
                'options' => ['placeholder' => DateTimeHelper::getFormatCNDateTime(time())],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'yyyy-MM-dd HH:i:ss',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'startDate' => $start_date,
                ]
            ]);?>
        </div>
        <!-- 活动结束时间 end -->
        <div class="col-lg-2"></div>
        <div class="col-lg-2">
            <label>
                <?= $model->attributeLabels()['production_date']; ?>
            </label>
        </div>
        <div>
            <?= DateTimePicker::widget([
                    'name' => 'GoodsActivity[production_date]',
                'value' => $model->production_date,
                'options' => ['placeholder' => DateTimeHelper::getFormatCNDateTime(time())],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'yyyy-MM-dd HH:i:ss',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ]
            ]);?>
        </div>
        <br />
        <?= $form->field($model, 'order_expired_time')->textInput() ?>

        <?php if ($model->act_type != GoodsActivity::ACT_TYPE_GROUP_BUY) : ?>
        <?= $form->field($model, 'buy_by_box')->radioList($buyByBoxMap) ?>

        <?= $form->field($model, 'number_per_box')->textInput() ?>
        <?php endif; ?>

        <?php //echo $form->field($model, 'restrict_amount')->textInput() ?>

        <?= $form->field($model, 'is_finished')->dropDownList($is_finished_map) ?>

        <?= $form->field($model, 'qr_code')->fileInput(['accept' => 'image/*']) ?>

        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

        <?= Html::img($model->getUploadUrl('qr_code'), ['height' => '200']) ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'act_desc')->textarea(['rows' => 6]) ?>

        <?php if ($model->act_type != GoodsActivity::ACT_TYPE_GROUP_BUY) : ?>
            <?= $form->field($model, 'shipping_code')->dropDownList($shippingCodeNameMap) ?>
        <?php endif; ?>

        <?php //echo $form->field($model, 'sample')->textInput() ?>

        <?php //echo $form->field($model, 'gift_integral')->textInput() ?>

        <?= $form->field($model, 'is_hot')->radioList($is_hot_map) ?>

        <?php //echo $form->field($model, 'deposit')->textInput() ?>

        <?php //echo $form->field($model, 'amount')->textInput() ?>

        <?php //echo $form->field($model, 'price')->textInput() ?>

        <?= $form->field($model, 'act_type')->hiddenInput(['value' => $model->act_type])->label('') ?>

        <?= $form->field($model, 'product_id')->hiddenInput()->label('') ?>

        <?= $form->field($model, 'goods_list')->fileInput(['accept' => 'image/*']) ?>
        <?= Html::img($model->getUploadUrl('goods_list'), ['height' => '200']) ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>

</script>