<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\dynagrid\DynaGrid;
use kartik\editable\Editable;
use common\helper\DateTimeHelper;
use common\models\Users;
use common\models\Region;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;

$spec_channel = Yii::$app->params['spec_channel'];
$province_has_server = Yii::$app->params['province_has_server'];
$city_has_server = Yii::$app->params['city_has_server'];
?>
<div class="users-index">
    <p>
        Tips: 用户名、省份名称标注
        (1)<span style="background: red">红色</span>表示所属省份已有服务商，但该用户没有绑定业务员<strong>没有真正转给服务商</strong>;
        (2)<span style="background: green">绿色</span>表示所属省份已有服务商，并且用户已绑定业务员;
        (3)<span style="background: yellow">黄色</span>表示特殊渠道用户，如：洽客
    </p>
    <p style="color: red">
        <strong>
            天津服务商 服务区域：天津市、秦皇岛市、唐山市、廊坊市，这四个城市的 已审核过的会员没有绑定服务商的，【确定要转移给天津服务商的时候】手动绑定到天津服务商名下，省市信息不要修改
        </strong>
    </p>
    <p>用户可用积分只在需要访问时更新，如用户的个人中心页、积分商城首页、后台用户详情页等</p>

    <?php  echo $this->render(
        '_search',
        [
            'model' => $searchModel,
        ]); ?>
    <p>
        <?= Html::a('添加用户', ['create'], ['class' => 'btn btn-success']) ?>

        <?php echo Html::a('导出当前搜索到的所有用户信息', str_replace(
            '/sc-user/index',
            '/sc-user/export',
            urldecode(Yii::$app->request->url)
        )) ?>
    </p>

    <?php
        $columns = [
//            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
            [
                'attribute' => 'user_name',
                'format' => 'raw',
                'value' => function($model) use ($spec_channel, $province_has_server, $city_has_server){
                    $str = '';
                    //  没绑定过手机号的微信用户名是加密的，其他是正常的
                    if ($model->openid && !$model->mobile_phone) {
                        $str.= base64_decode($model->user_name);
                    } else {
                        $str.= $model->user_name;
                    }
                    //  有昵则显示
                    if ($model->nickname) {
                        $str.= ' |姓名：'.$model->nickname;
                    }

                    if (in_array($model->channel, $spec_channel)) {
                        $str = '<span style="background:yellow">'.$str.'</span>';
                    } elseif (in_array($model->province, $province_has_server) ||
                        in_array($model->city, $city_has_server))
                    {
                        if ($model->servicer_user_id) {
                            $str = '<span style="background:green">'.$str.'</span>';
                        } else {
                            $str = '<span style="background:red">'.$str.'</span>';
                        }
                    }

                    return $str;
                },
            ],

            'mobile_phone',
            'company_name',

            [
                'attribute' => 'user_type',
                'value' => function($model) {
                    return Users::$user_type_map[$model->user_type];
                }
            ],

            [
                'attribute' => 'reg_time',
                'value' => function($model){
                    return DateTimeHelper::getFormatCNDateTime($model->reg_time);
                },
            ],
            [
                'attribute' => 'last_login',
                'value' => function($model){
                    return DateTimeHelper::getFormatCNDateTime($model->last_login);
                },
            ],
            'visit_count',
            [
                'attribute' => 'is_checked',
                'format' => 'html',
                'value' => function($model){
                    return '<a href="" title="'.$model->checked_note.'">'.Users::$is_checked_img_map[$model->is_checked].'</a>';
                }
            ],
            [
                'attribute' => 'user_rank',
                'value' => function($model){
                    $rankMap = Users::$user_rank_map;
                    return isset($rankMap[$model->user_rank]) ? $rankMap[$model->user_rank] : '';
                }
            ],
            [
                'attribute' => 'checked_note',
                'format' => 'raw',
                'value' => function($model){
                    if (empty($model->checked_note)) {
                        return '';
                    }
                    $shortContent = $model->checked_note;
                    if (mb_strlen($model->checked_note, 'utf-8') > 10) {
                        $shortContent = mb_substr($model->checked_note, 0, 10, 'utf-8');
                        $shortContent .= '...';
                    }

                    $content = '<p class="text-justify">' .
                        $model->checked_note .
                        '</p>';
                    // medium
                    return PopoverX::widget([
                        'header' => '审核详情',
                        'size' => PopoverX::SIZE_MEDIUM,
                        'placement' => PopoverX::ALIGN_RIGHT,
                        'content' => $content,
                        'toggleButton' => ['label'=>$shortContent, 'class'=>'btn btn-default'],
                    ]);
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'province',
                'pageSummary' => 'Total',
                'vAlign' => 'middle',
                'width' => '210px',
                'editableOptions' => function ($model, $key, $index) use ($provinceMap) {
                    return [
                        'header' => '选择所属省份',
                        'size' => 'md',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
//                        'format' => Editable::FORMAT_BUTTON,  //  显示可编辑按钮，但是颜色没有高亮
                        'data' => $provinceMap,
                        'widgetClass'=> 'kartik\datecontrol\DateControl',

                        'formOptions' => [
                            'method' => 'post',
                            'action' => Url::to('/sc-user/edit-province'),
                        ],
                    ];
                },
                'format' => 'raw',
                'value' => function($model) use ($spec_channel, $province_has_server, $provinceMap){
                    $province = isset($provinceMap[$model->province]) ? $provinceMap[$model->province] : '未设置';

                    if (in_array($model->channel, $spec_channel)) {
                        $province = '<span style="background:yellow">'.$province.'</span>';
                    } elseif (in_array($model->province, $province_has_server)) {
                        if ($model->servicer_user_id) {
                            $province = '<span style="color:green">'.$province.'</span>';
                        } else {
                            $province = '<span style="color:red">'.$province.'</span>';
                        }
                    }
                    return $province;
                }
            ],
            [
                'attribute' => 'city',
                'value' => function($model){
                    return Region::getRegionName($model->city);
                }
            ],
            [
                'attribute' => 'totalAmount',
                'value' => function($model) {
                    return $model->totalAmount;
                }
            ],
            'tmpIntBalance',
            'payTimes',
            [
                'attribute' => 'lastPayTime',
                'value' => function($model) {
                    if ($model->lastPayTime) {
                        return DateTimeHelper::getFormatCNDateTime($model->lastPayTime);
                    } else {
                        return '没支付过';
                    }
                }
            ],
            [
                'label' => '是否认证',
                'value' => function ($model) {
                    if(empty($model->extension)) {
                        $res = '没有认证';
                    } elseif($model->extension['identify'] == 0) {
                        $res = '<a style="color:orange" href="">待认证</a>';
                    } elseif($model->extension['identify'] == 1) {
                        $res = '<span style="color:green">已认证</span>';
                    } elseif($model->extension['identify'] == 2) {
                        $res = '<span style="color:sandybrown">拒绝认证</span>';
                    }
                    return $res;
                },
                'format' => 'raw'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update} | {check}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => '查看',
                            'target' => '_blank',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => '更新',
                            'target' => '_blank',
                        ]);
                    },
                    'check' => function ($url, $model, $key) {
                        return Html::a(
                                '<span class="glyphicon glyphicon-question-sign"></span>',
                                $url,
                                [
                                    'title' => '审核',
                                    'target' => '_blank'
                                ]
                            );
                    },
                ],
            ],
        ];
        echo DynaGrid::widget([
            //        'dataProvider' => $dataProvider,
            //        'filterModel' => $searchModel,
            'columns' => $columns,
            'storage' => DynaGrid::TYPE_COOKIE,
            'theme' => 'panel-primary',
//            'pjax' => true,
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'heading' => '<h3 class="panel-title">零售店列表</h3>',
                ],
                'toolbar' =>  [
                    ['content'=>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                    ],
                    ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                    '{toggleData}',
                ]
            ],
            'options' => [
                'id' => 'dynagrid-sc-user',
            ],
        ]);
    ?>


</div>
