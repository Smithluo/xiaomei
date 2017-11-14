<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\dashboard\MarkSearch */
/* @var $form yii\widgets\ActiveForm */

//  开始时间为30天前 3600 * 24 * 30 = 2592000
$start_date = date('Y-m-d', strtotime('-1 year'));

?>

<div class="mark-search">

    <?php $form = ActiveForm::begin([
        'action' => [$back_action],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-6 control-label text-right'],
        ],
    ]); ?>
    <div class="row">
        <div class=" col-lg-2">
            <?php echo $form->field($model, 'plat_form')->dropDownList($platFormMap, ['prompt' => '请选择']) ?>
            <?php echo $form->field($model, 'click_times')->label('点击量大于') ?>
        </div>
        <div class=" col-lg-2">
            <?php echo $form->field($model, 'order_count')->label('订单数量大于') ?>
            <br />
            <?php echo $form->field($model, 'pay_count')->label('支付单数大于') ?>
        </div>

        <div class="form-group col-lg-2">
            <?php echo $form->field($model, 'user_id') ?>
            <br />
            <?php echo $form->field($model, 'login_times')->label('登录次数大于') ?>
        </div>

        <div class="col-lg-4">
            <!-- 查询开始时间 start -->
            <div class="col-lg-3">
                <label>
                    <?= $model->attributeLabels()['start_time']; ?>
                </label>
            </div>
            <div class="col-lg-7">
                <?= DatePicker::widget([
                    'name' => 'MarkSearch[start_time]',
                    'value' => date('Y-m-d', DateTimeHelper::getFormatCNTimesTimestamp($search_start)),
                    'options' => ['placeholder' => DateTimeHelper::getFormatCNDate(time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'yyyy-MM-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'startDate' => $start_date,
                    ]
                ]);?>
            </div>
            <!-- 查询开始时间 end -->

            <!-- 查询结束时间 start -->
            <div class="col-lg-3">
                <label>
                    <?= $model->attributeLabels()['end_time']; ?>
                </label>
            </div>
            <div class="col-lg-7">
                <?= DatePicker::widget([
                    'name' => 'MarkSearch[end_time]',
                    'value' => date('Y-m-d', DateTimeHelper::getFormatCNTimesTimestamp($search_end)),
                    'options' => ['placeholder' => DateTimeHelper::getFormatCNDate(time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'yyyy-MM-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'startDate' => $start_date,
                    ]
                ]);?>
            </div>
            <!-- 查询结束时间 end -->

        </div>

        <div class="form-group col-lg-2">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?php //echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<?php //echo $form->field($model, 'id') ?>


