<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\OrderInfo;

/* @var $this yii\web\View */
/* @var $model common\models\ServicerDivideRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ibox-content m-b-sm border-bottom">

    <div class="row">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'enableClientScript' => false,
        'fieldClass' => 'common\widgets\ActiveField',
    ]); ?>

    <?= $form->field($model, 'date_added', [
        'template'=>'
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {input}
                    </div>
                </div>
            </div>',
        'labelOptions'=>['class'=>'control-label', 'for'=>'date_added'],
        'inputOptions'=>['id'=>'date_added', 'class'=>'form-control'],
    ]) ?>

    <?= $form->field($model, 'date_modified', [
        'template'=>'
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {input}
                    </div>
                </div>
            </div>',
        'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
        'inputOptions'=>['id'=>'date_modified', 'class'=>'form-control'],
    ]) ?>

    <?= $form->field($model, 'servicer_user_name', [
        'template'=>'
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        {input}
                    </div>
                </div>
            </div>
            ',
        'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
        'inputOptions'=>['id'=>'userName', 'class'=>'form-control'],
    ]) ?>

    <?= $form->field($model, 'order_sn', [
        'template'=>'
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date">
                        {input}
                    </div>
                </div>
            </div>
            ',
        'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
        'inputOptions'=>['id'=>'orderId', 'class'=>'form-control'],
    ]) ?>

    <?= $form->field($model, 'order_status', [
        'template' => '
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date col-sm-12">
                        <select class="form-control m-b" name="ServicerDivideRecordSearch[order_status]" id="bizType">
                            <option value="0">全部订单</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_SHIPPED. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_SHIPPED ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_SHIPPED]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_COMPLETED. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_COMPLETED ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_COMPLETED]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_COMPLETED_OVER. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_COMPLETED_OVER ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_COMPLETED_OVER]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_REFUNDED_DONE. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_REFUNDED_DONE ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_REFUNDED_DONE]. '</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_RETURNED_DONE. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_RETURNED_DONE ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_RETURNED_DONE].'</option>
                            <option value="'. OrderInfo::ORDER_CS_STATUS_RETURNED. '" '. ($model->order_status == OrderInfo::ORDER_CS_STATUS_RETURNED ? 'selected':'').'>'. OrderInfo::$order_cs_status_map_no_style[OrderInfo::ORDER_CS_STATUS_RETURNED]. '</option>
                        </select>
                    </div>
                </div>
            </div>
        ',
        'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
    ]
    ) ?>


    <?= Html::submitButton('筛选', ['class' => 'btn btn-w-m btn-pink', 'style' => 'margin-top: 22px;']) ?>

    <?php ActiveForm::end(); ?>
    </div>
</div>
