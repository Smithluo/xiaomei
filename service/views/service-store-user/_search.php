<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model service\models\UsersSearch */
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
    <div class="row">

    <?php
        if ($checked) {
            $action = \yii\helpers\Url::to(['/service-store-user/index', 'is_checked' => 2]);
        } else {
            $action = \yii\helpers\Url::to(['/service-store-user/index', 'is_checked' => 0]);
        }

    $form = ActiveForm::begin([
        'action' => $action,
        'method' => 'get',
        'enableClientScript' => false,
        'fieldClass' => 'common\widgets\ActiveField',
    ]);
    ?>

    <?=
    $form->field($model, 'user_id', [
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

    <?=
    $form->field($model, 'company_name', [
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

    <?= $form->field($model, 'user_name', [
        'template' => '
                    <div class="col-sm-2">
                        <div class="form-group">
                            {label}
                            <div class="input-group date">
                                {input}
                            </div>
                        </div>
                    </div>
                    ',
        'labelOptions' => ['class' => 'control-label', 'for' => 'date_added'],
        'inputOptions' => ['id' => 'date_added', 'class' => 'form-control'],
    ])->label('联系人');
    ?>

    <?=
    $form->field($model, 'mobile_phone', [
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
    ])->label('联系电话');
    ?>
        <?php if($_GET['is_checked']==0){?>
        <div class="col-lg-2">
            <?= $form->field($model,'citycode')->dropDownList($res,['prompt'=>'请选择服务城市'])->label('门店所在区域')?>
        </div>
        <?php }?>

        <?php
            if ($checked) {
                if(Yii::$app->user->can('service_boss') || Yii::$app->user->can('service_manager')){
        ?>
            <?=
            $form->field($model, 'servicer_user_name', [
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
        <?php }} ?>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-w-m btn-pink', 'style' => 'margin-top: 22px;']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>
