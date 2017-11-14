<?php

use yii\helpers\Html;
use common\widgets\GridView;
use common\models\OrderInfo;
use common\models\OrderGroup;
use common\helper\NumberHelper;
use service\models\Users;
use service\assets\UserAsset;

UserAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel service\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '门店列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/storeMange';

?>
<div class="wrapper wrapper-content animated fadeInRight">
<?php echo $this->render('_search', [
    'model' => $searchModel,
    'checked' => $checked
]); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'dataColumnClass' => \common\widgets\DataColumn::className(),
    'rowOptions' => function ($model, $key, $index, $grid) {
        if($index % 2 == 0) {
            return ['class'=>'footable-even footable-detail-show', 'style'=>'display: table-row;'];
        }
        else {
            return ['class'=>'footable-odd footable-detail-show', 'style'=>'display: table-row;'];
        }
    },
    'columns' => [
        [
            'label'=>'门店ID',
            'encodeLabel' => false,
            'attribute'=>'user_id',
            'format'=>'raw',
            'value'=>function($model) {
                return $model->user_id;
            },
            'filter'=>Html::activeTextInput($searchModel, 'user_id', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页
            'footer' => '
                                    <td colspan="11">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',
        ],
        [
            'label'=>'门店名称',
            'encodeLabel' => false,
            'attribute'=>'company_name',
            'format'=>'raw',
            'value'=>function($model) {
                return $model->company_name;
            },
            'filter'=>Html::activeTextInput($searchModel, 'company_name', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'注册时间',
            'encodeLabel' => false,
            'attribute'=>'reg_time',
            'format'=>'raw',
            'value'=>function($model) {
                return \common\helper\DateTimeHelper::getFormatCNDateTime($model->reg_time);
            },
            'filter'=>Html::activeTextInput($searchModel, 'reg_time', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'会员等级',
            'encodeLabel' => false,
            'attribute'=>'user_rank',
            'format'=>'raw',
            'value'=>function($model) {
                return !empty($model->user_rank) ? Users::$user_rank_map[$model->user_rank]: null;
            },
            'filter'=>Html::activeTextInput($searchModel, 'company_name', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'联系人',
            'encodeLabel' => false,
            'attribute'=>'user_name',
            'format'=>'raw',
            'value'=>function($model) {
                return (empty($model['nickname']) ? $model['user_name']: $model['nickname']);
            },
            'filter'=>Html::activeTextInput($searchModel, 'consignee', ['class' => 'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'联系电话',
            'encodeLabel' => false,
            'attribute'=>'mobile_phone',
            'format'=>'raw',
            'value'=>function($model) {
                return $model->mobile_phone;
            },
            'filter'=>Html::activeTextInput($searchModel, 'mobile', ['class' => 'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'最后一次采购',
            'encodeLabel' => false,
            'attribute'=>'last_order_time',
            'format'=>'raw',
            'value'=>function($model) {
//                $result = OrderInfo::find()->select('add_time')->where(['user_id' => $model->user_id])->one();
//                return Yii::$app->formatter->asDate($result['add_time'], 'yyyy-MM-dd');
                if(!empty($model->lastOrder)){
                    return Yii::$app->formatter->asDate($model->lastOrder['add_time'], 'yyyy-MM-dd');
                }
                return null;
            },
            'filter'=>Html::activeTextInput($searchModel, 'last_order_time', ['class' => 'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'累计采购金额',
            'encodeLabel' => false,
            'attribute'=>'total_amount',
            'format'=>'raw',
            'value' => function($model) {
                return NumberHelper::price_format($model->total_amount - $model->total_discount);
            },
            'filter'=>Html::activeTextInput($searchModel, 'total_amount', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页
        ],
        [
            'label'=>'所属业务员',
            'encodeLabel' => false,
            'attribute'=>'servicer_user_name',
            'format'=>'raw',
            'value'=>function($model) {
                return $model->nickname;
            },
            'filter'=>Html::activeTextInput($searchModel, 'servicer_user_name', ['class'=>'form-control']),
            'enableSorting' => false, //客户端分页

        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            //业余员不能看到变更业余员按钮
            'template' => '<div class="btn-group" >{view}'.(Yii::$app->user->can('service_saleman') ? '' :'{change}' ).'</div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return  Html::a(
                        '门店详情',
                        $url,
                        [
                            'class' => 'btn btn-outline btn-primary',
                        ]
                    );
                },
                'change' => function ($url,$model,$key)
                {
                    return Html::a('变更业务员',
                        'javascript:;',
                        [
                            'class' => 'btn btn-outline btn-pink',
                            'xm-action' => 'changeUser',
                            'xm-data' => $model->user_id,
                        ]);
                }
            ],
        ],

    ],
]); ?>
</div>
    </div>
</div>
</div>




