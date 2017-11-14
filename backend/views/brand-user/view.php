<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\BrandUser */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => '查看', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-user-view">

    <h1><?= Html::encode('查看品牌商信息') ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_id',
            'email:email',
            'user_name',
//            'password',
//            'question',
//            'answer',
//            'sex',
//            'birthday',
//            'user_money',
//            'frozen_money',
//            'pay_points',
//            'rank_points',
//            'address_id',
//            'zone_id',
            [
                'attribute' => 'reg_time',
                'value' => DateTimeHelper::getFormatCNDateTime($model->reg_time)
            ],
//            'last_login',
//            'last_time',
//            'last_ip',
//            'visit_count',
//            'user_rank',
//            'is_special',
//            'ec_salt',
//            'salt',
//            'parent_id',
//            'flag',
//            'alias',
//            'msn',
            'qq',
            'office_phone',
//            'home_phone',
            'mobile_phone',
            'company_name',
//            'is_validated',
//            'credit_line',
//            'passwd_question',
//            'passwd_answer',
//            'headimgurl:url',
//            'openid',
//            'qq_open_id',
//            'aite_id',
//            'unionid',
//            'wx_pc_openid',
//            'licence_image',
//            'servicer_user_id',
//            'servicer_super_id',
            [
                'attribute' => 'brand_id_list',
                'format' => 'html',
                'value' => $brand_name_list,
            ],
//            'auth_key',
//            'access_token',
//            'servicer_info_id',
            'bank_info_id',
            'brand_admin_id',
            'nickname',
        ],
    ]) ?>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认要删除这个品牌商吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
