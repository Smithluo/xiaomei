<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3 0003
 * Time: 14:58
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\OrderGroup;
?>
<div class="ibox-content border-bottom">
    <div class="row">
        <?php
            $form = ActiveForm::begin([
                'action' => ['view'],
                'method' => 'get',
                'enableClientScript' => false,
                'fieldClass' => 'common\widgets\ActiveField',
            ]);
        ?>
        <?=
        $form->field($model, 'date_added', [
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
            'labelOptions'=>['class'=>'control-label', 'for'=>'date_added'],
            'inputOptions'=>['id'=>'date_added', 'class'=>'form-control'],
        ])
        ?>
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

        <?= $form->field($model, 'group_id', [
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
        <?= $form->field($model, 'group_status', [
                'template' => '
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date col-sm-12">
                        <select class="form-control m-b" name="ServiceUserSearch[group_status]" id="bizType">
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_ALL.'" '.(!isset($model->group_status) || $model->group_status == OrderGroup::ORDER_GROUP_STATUS_ALL ? 'selected':'').'>全部订单</option>
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_UNPAY. '" '. (isset($model->group_status) && $model->group_status == OrderGroup::ORDER_GROUP_STATUS_UNPAY ? 'selected':'').'>'. OrderGroup::$order_group_status[OrderGroup::ORDER_GROUP_STATUS_UNPAY]. '</option>
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_PAID. '" '. (isset($model->group_status) && $model->group_status == OrderGroup::ORDER_GROUP_STATUS_PAID ? 'selected':'').'>'. OrderGroup::$order_group_status[OrderGroup::ORDER_GROUP_STATUS_PAID]. '</option>
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_HANDLING. '" '. (isset($model->group_status) && $model->group_status == OrderGroup::ORDER_GROUP_STATUS_HANDLING ? 'selected':'').'>'. OrderGroup::$order_group_status[OrderGroup::ORDER_GROUP_STATUS_HANDLING]. '</option>
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_FINISHED. '" '. (isset($model->group_status) && $model->group_status == OrderGroup::ORDER_GROUP_STATUS_FINISHED ? 'selected':'').'>'. OrderGroup::$order_group_status[OrderGroup::ORDER_GROUP_STATUS_FINISHED]. '</option>
                            <option value="'. OrderGroup::ORDER_GROUP_STATUS_CANCELED. '" '. (isset($model->group_status) && $model->group_status == OrderGroup::ORDER_GROUP_STATUS_CANCELED ? 'selected':'').'>'. OrderGroup::$order_group_status[OrderGroup::ORDER_GROUP_STATUS_CANCELED]. '</option>
                        </select>
                    </div>
                </div>
            </div>
        ',
                'labelOptions'=>['class'=>'control-label', 'for'=>'date_modified'],
            ]
        ) ?>

        <?= $form->field($model, 'mobile', [
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
        <?= Html::hiddenInput('id',$model->id) ?>

        <?= Html::submitButton('筛选', ['class' => 'btn btn-w-m btn-pink', 'style' => 'margin-top: 22px;']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
