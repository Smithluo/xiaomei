<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14 0014
 * Time: 17:50
 */

namespace common\behaviors;

use Yii;
use common\helper\DateTimeHelper;
use common\models\OrderAction;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class RecordOrderActionBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'insert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'update',
        ];
    }

    public function insert() {

        $orderInfo = $this->owner;

        $orderAction = new OrderAction();
        $orderAction->order_id = $orderInfo->order_id;
        $orderAction->action_place = 2;
        $orderAction->action_note = $orderInfo->postscript;
        if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
            $orderAction->action_user = Yii::$app->user->identity['user_name'];
        }
        else {
            $orderAction->action_user = '未知用户(可能是批量修改的脚本修改了状态)';
        }
        $orderAction->order_status = $orderInfo->order_status;
        $orderAction->pay_status = $orderInfo->pay_status;
        $orderAction->shipping_status = $orderInfo->shipping_status;
        $orderAction->log_time = DateTimeHelper::gmtime();

        $orderAction->save();
    }

    public function update($event) {
        $changedAttributes = $event->changedAttributes;
        $orderInfo = $this->owner;
        foreach ($changedAttributes as $key => $value) {
            if (in_array($key, [
                'order_status',
                'pay_status',
                'shipping_status',
            ]) || !empty($orderInfo->note)) {
                $orderAction = new OrderAction();
                $orderAction->order_id = $orderInfo->order_id;
                $orderAction->action_place = 2;
                $orderAction->action_note = $orderInfo->note;

                if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
                    $orderAction->action_user = Yii::$app->user->identity['user_name'];
                }
                else {
                    $orderAction->action_user = '未知用户(可能是批量修改的脚本修改了状态)';
                }
                $orderAction->order_status = $orderInfo->order_status;
                $orderAction->pay_status = $orderInfo->pay_status;
                $orderAction->shipping_status = $orderInfo->shipping_status;
                $orderAction->log_time = DateTimeHelper::gmtime();

                $orderAction->save();
                break;
            }
        }
    }
}