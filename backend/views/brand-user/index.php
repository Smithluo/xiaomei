<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Brand;
use common\models\BrandUser;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '创建品牌商';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            [
//                'class' => 'yii\grid\SerialColumn',
//                'header' => '序号'
//            ],

            'user_id',
            'email:email',
            'user_name',
            'nickname',
//            'password',
//            'question',
            // 'answer',
//             'sex',
            // 'birthday',
            // 'user_money',
            // 'frozen_money',
            // 'pay_points',
            // 'rank_points',
            // 'address_id',
            // 'zone_id',
//             'reg_time',
//             'last_login',
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
             'qq',
            [
                'label' => '联系电话',
                'format' => 'html',
                'value' => function($model){
                    $contact = '';
                    if ($model->office_phone) {
                        $contact .= $model->office_phone.'<br />';
                    }
                    if ($model->home_phone) {
                        $contact .= $model->home_phone.'<br />';
                    }
                    if ($model->mobile_phone) {
                        $contact .= $model->mobile_phone;
                    }

                    return $contact;
                }
            ],
//             'office_phone',
//             'home_phone',
//             'mobile_phone',
             'company_name',
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
            // 'servicer_user_id',
            // 'servicer_super_id',
            [
                'attribute' => 'brand_id_list',
                'label' => '旗下品牌',
                'format' => 'html',
                'value' => function($model){
                    $brand_id_list = BrandUser::getBrandIdList($model->user_id);
                    $brand_name_list = Brand::getBrandListMap($brand_id_list);
                    return implode('<br />', $brand_name_list);
                }
            ],
            // 'auth_key',
            // 'access_token',
            // 'servicer_info_id',
//             'bank_info_id',
            // 'brand_admin_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update}'
            ],
        ],
    ]); ?>
</div>
