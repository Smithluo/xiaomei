<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\OrderGroup;

/* @var $this yii\web\View */
/* @var $model common\models\ServicerDivideRecordSearch */
/* @var $form yii\widgets\ActiveForm */

$provinces = \common\helper\CacheHelper::getRegionCache([
    'type' => 'tree',
    'ids' => [],
    'deepth' => 0
]);
//拿到服务商的区域
if(Yii::$app->user->can('service_manager'))
{
    $regionList = \common\models\UserRegion::find()
        ->select(['region_id'])
        ->where(['user_id' => Yii::$app->user->identity['servicer_super_id']])
        ->asArray()
        ->all();
}
else
{
    $regionList = \common\models\UserRegion::find()
        ->select(['region_id'])
        ->where(['user_id' => Yii::$app->user->identity['user_id']])
        ->asArray()
        ->all();
}

$regionArray=[];

foreach($regionList as $v)
{
    $regionArray[]=$v['region_id'];
}
$res = [];
\common\helper\CacheHelper::getCityArrayFromTree( $res, $provinces,$regionArray);
?>

<div class="ibox-content m-b-sm border-bottom">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'enableClientScript' => false,
        'fieldClass' => 'common\widgets\ActiveField',
    ]); ?>
    <div class="row">

<!--    开始时间    -->
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
<!--    结束时间    -->
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
<!--    按照业务员查询 -->
        <?php if(Yii::$app->user->can('service_boss') || Yii::$app->user->can('service_manager')):?>
    <?= $form->field($model, 'consignee', [
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
        <?php endif;?>
        <?= $form->field($model, 'nickname', [
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
<!--    大单号 -->
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
        ])->label('收货人电话') ?>

    <?= $form->field($model, 'group_status', [
        'template' => '
            <div class="col-sm-2">
                <div class="form-group">
                    {label}
                    <div class="input-group date col-sm-12">
                        <select class="form-control m-b" name="ServiceOrderGroupSearch[group_status]" id="bizType">
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


        <div class="col-lg-2">
            <?= $form->field($model,'citycode')->dropDownList($res,[
                'prompt' => '请选择城市'
            ])->label('城市')?>
        </div>
        <?= Html::submitButton('筛选', ['class' => 'btn btn-w-m btn-pink', 'style' => 'margin-top: 22px;']) ?>



    </div>
    <?php ActiveForm::end(); ?>
</div>
