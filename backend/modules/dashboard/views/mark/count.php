<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\dashboard\MarkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户行为数据统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mark-index">
    <h2><?= Html::encode($this->title).' | 本功能生效始于2016年11月18日' ?> <a href="/dashboard/mark/index">查看原始数据</a></h2>
    <h3>(内部用户不纳入统计)查询范围：<?=$period?> 天 | 复购率：<?=$repeat_percent?> | 转化率：<?=$change_percent?></h3>
    <?php  echo $this->render('_search', [
        'model' => $searchModel,
        'back_action' => $back_action,
        'search_start' => $search_start,
        'search_end' => $search_end,
        'platFormMap' => $platFormMap,
    ]); ?>

    <table class="table table-bordered">
        <caption>统计表(后面会补充分页功能)</caption>
        <thead>
            <tr>
                <td>活跃用户总计: <?=$count?></td>
                <td>支付次数最多：<?=$total['pay_times_max']?></td>
                <td>下单次数最多：<?=$total['order_times_max']?></td>
                <td>点击量总数：<?=$total['click_times']?></td>
                <td></td>
                <td>登录天数总计：<?=$total['login_days']?></td>
                <td>登录总次数：<?=$total['login_times']?></td>
                <td>下单总次数：<?=$total['order_times']?></td>
                <td>下单总数：<?=$total['order_count']?></td>
                <td>支付总次数：<?=$total['pay_times']?></td>
                <td>支付总单数：<?=$total['pay_count']?></td>
            </tr>
            <tr>
                <th>用户ID</th>
                <th>用户名</th>
                <th>手机号</th>
                <th>平台</th>
                <th>点击量</th>
                <th>登录天数</th>
                <th>登录次数</th>
                <th>下单天数</th>
                <th>下单次数</th>
                <th>支付天数</th>
                <th>支付次数</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $user_id => $item) : ?>
            <tr>
                <td><?=$item['user_id']?></td>
                <td><?=$item['user_name']?></td>
                <td><?=$item['mobile_phone']?></td>
                <td><?=$platFormMap[$item['plat_form']]?></td>
                <td><?=$item['click_times']?></td>
                <td><?=$item['login_days']?></td>
                <td><?=$item['login_times']?></td>
                <td><?=$item['order_times']?></td>
                <td><?=$item['order_count']?></td>
                <td><?=$item['pay_times']?></td>
                <td><?=$item['pay_count']?></td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>
