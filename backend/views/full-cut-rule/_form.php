<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\detail\DetailView;
use backend\models\FullCutRule;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\FullCutRule */
/* @var $form yii\widgets\ActiveForm */

$attributes = [
    [
        'columns' => [
            [
                'attribute' => 'rule_name',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'event_id',
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => $eventList,
                    'options' => ['placeholder' => '选择活动'],
                    'pluginOptions' => ['width' => '100%'],
                ],
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'above',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'cut',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'status',
                'type' => DetailView::INPUT_SWITCH,
                'value' => $model->status ? '启用' : '不启用',
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => '启用',
                        'offText' => '不启用',
                    ]
                ],
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
];

if (!empty($couponRecordIssueForm) && $model->status = FullCutRule::STATUS_VALID) {
    $attributes[] = [
        'columns' => [
            [
                'attribute' => 'term_of_validity',
                'value' => $model->term_of_validity > 0
                    ? DateTimeHelper::getTimeDesc($model->term_of_validity)
                    : '优惠券的可用时段与领取优惠券时的活动生效时段一致',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ];
}
?>

<div class="row">
    <div class="col-lg-5">
        <p style="color:red">
            tips:<strong>满减/优惠券 的数额都设置为整数</strong>。满减活动当前只支持一个，活动结束，要把所有的规则置为失效，活动关联的商品取消
        </p>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
            'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'deleteOptions'=>[ // your ajax delete parameters
                'params' => ['id' => $model->rule_id, 'custom_param' => true],
            ],
            'panel'=>[
                'heading'=>'活动详情：' . $model->rule_name,
                'type'=> DetailView::TYPE_PRIMARY,
            ],

            'formOptions' => [
                'action' => $model->isNewRecord
                    ? \yii\helpers\Url::to(['create'])
                    : \yii\helpers\Url::to(['update', 'id' => $model->rule_id]),
            ],

            'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
        ]);
        ?>
    </div>

    <div class="col-lg-7">
        <pre>
    领券后有效时间(s) 填写时间的秒数。
    举例：优惠券在【用户领券或后台绑定券给用户】后1天内有效，则填些 86400
    1时      = 3600
    1天      = 86400
    1周      = 604800
    10天     = 864000
    2周      = 1209600
    15天     = 1296000
    30天     = 2592000
    31天     = 2678400
</pre>
    </div>
</div>


<?php
    if ($model->status) :
?>
<div class="row">
    <div class="col-lg-4">
        <?php
        //  如果是优惠券，则给出发行按钮
        if (!empty($couponRecordIssueForm) && $model->status = FullCutRule::STATUS_VALID) {
            echo '<h2>发行优惠券：'.$coupon['event_name'].'</h2>';
            echo '<h3>已发行数量：'.$coupon['circulation'].'</h3>';
            echo '<h3>已领取数量：'.$coupon['bindCount'].'</h3>';
            echo '<h3>已使用数量：'.$coupon['usedCount'].'</h3>';

            echo '<h3 style="color: red">建议每次的发行数量在500以内，避免页面执行超时</h3>';
            $form = ActiveForm::begin([
                'action' => ['issue'],
                'method' => 'get',
                'fieldConfig' => [
                    'template' => "<div class='row'>
                    {label}\n
                    <div class=\"col-lg-4\">{input}</div>\n
                    <div class=\"col-lg-2\"></div>
                    <div class=\"col-lg-4\">{error}</div>
                </div>",
                    'labelOptions' => ['class' => 'col-lg-2 control-label text-right'],
                ],
            ]);

            echo $form->field($couponRecordIssueForm, 'event_id')->hiddenInput()->label('');
            echo $form->field($couponRecordIssueForm, 'rule_id')->hiddenInput()->label('');
            echo $form->field($couponRecordIssueForm, 'number');
            echo Html::submitButton('发行', ['class' => 'btn btn-primary']);

            ActiveForm::end();
        }
        ?>
    </div>
    <div class="col-lg-8"></div>
</div>
<?php
    endif;
?>