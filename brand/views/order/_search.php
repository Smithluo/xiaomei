<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;
/* @var $this yii\web\View */
/* @var $model common\models\OrderInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-lg-8">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="date_added">开始时间</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input id="date_added" name='start_date' type="text" class="form-control" value="<?=is_numeric($params['start_date']) ? DateTimeHelper::getFormatDate($params['start_date']) : $params['start_date']?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="date_modified">结束时间</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input id="date_modified" name='end_date' type="text" class="form-control" value="<?=is_numeric($params['end_date']) ? DateTimeHelper::getFormatDate($params['end_date']) : $params['end_date']?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label" for="orderinfosearch-mobile">手机号</label>
                            <input type="text" id="orderinfosearch-mobile" class="form-control" name="OrderInfoSearch[mobile]" value="<?=$model->mobile?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label" for="orderinfosearch-order_sn">订单编号</label>
                            <input type="text" id="orderinfosearch-order_sn" class="form-control" name="OrderInfoSearch[order_sn]" value="<?=$model->order_sn?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label" for="orderinfosearch-consignee">收货人</label>
                            <input type="text" id="orderinfosearch-consignee" class="form-control" name="OrderInfoSearch[consignee]" value="<?=$model->consignee?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <?= Html::submitButton('筛选', [
                    'class' => 'btn btn-w-m btn-primary',
                    'style' => "margin-top: 23px;margin-left:20px;"
                ]) ?>
                <?= Html::a('重置', ['index'], [
                    'class' => 'btn btn-w-m  btn-default',
                    'style' => "margin-top: 23px;margin-left:20px;"
                ]) ?>

            </div>



        </div>
    </div>
    <?php ActiveForm::end(); ?>

