<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ServicerUserInfo;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '服务商列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Users', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('升级', ['upgrade'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'user_id',
            'user_name',
            [
                'label'=>'店铺名称',
                'attribute'=>'company_name',
                'format'=>'raw',
                'value'=>function($model) {
                    return $model->company_name;
                },
            ],
            // 'sex',
            // 'birthday',
            // 'user_money',
            // 'frozen_money',
            // 'pay_points',
            // 'rank_points',
            // 'address_id',
            // 'zone_id',
            // 'reg_time',
            // 'last_login',
            // 'last_time',
            // 'last_ip',
            // 'visit_count',
            // 'user_rank',
            // 'is_special',
            // 'ec_salt',
            // 'salt',
            // 'parent_id',
            // 'flag',
            // 'alias',
            // 'msn',
            // 'qq',
            // 'office_phone',
            // 'home_phone',
             'mobile_phone',
            // 'is_validated',
            // 'credit_line',
            // 'passwd_question',
            // 'passwd_answer',
            // 'headimgurl:url',
            // 'openid',
            // 'qq_open_id',
            // 'aite_id',
            // 'unionid',
            // 'wx_pc_openid',
            // 'licence_image',
            // 'servicer_info_id',
            // 'auth_key',
            // 'access_token',
             'int_balance',
            [
                'label' => '上级服务商',
                'value' => function($model) {
                    if ($model->supserServicerUser) {
                        return $model->supserServicerUser->showName. ' --- '. $model->supserServicerUser->mobile_phone. ' --- '. $model->supserServicerUser->company_name;
                    }
                    return '';
                }
            ],
            'mobile_phone',
            'int_balance',
            [
                'label' => '服务编码',
                'value' => function($model) {
                    if ($model->servicer_info_id) {
                        $service_user_info = ServicerUserInfo::find()->where(['id' => $model->servicer_info_id])->one();
                        if ($service_user_info) {
                            return $service_user_info->id.' : '.$service_user_info->servicer_code;
                        }
                    }
                    return '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
