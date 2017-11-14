<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14 0014
 * Time: 17:50
 */

namespace common\behaviors;

use common\models\OrderGroup;
use common\models\OrderInfo;
use Yii;
use common\helper\DateTimeHelper;
use common\models\OrderModifyAction;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class RecordOrderModifyActionBehavior extends Behavior
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

        $orderAction = new OrderModifyAction();
        if ($orderInfo instanceof OrderGroup) {
            $orderAction->group_id = $orderInfo['id'];
        }
        elseif ($orderInfo instanceof OrderInfo) {
            $orderAction->order_id = $orderInfo['order_id'];
        }

        if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
            $orderAction->action_user = Yii::$app->user->identity['user_name'];
        }
        else {
            $orderAction->action_user = '未知用户(可能是批量修改的脚本修改了状态)';
        }

        $orderAction->user_id = $orderInfo['user_id'];
        $orderAction->consignee = $orderInfo['consignee'];
        $orderAction->mobile = $orderInfo['mobile'];
        $orderAction->province = $orderInfo['province'];
        $orderAction->city = $orderInfo['city'];
        $orderAction->district = $orderInfo['district'];
        $orderAction->address = $orderInfo['address'];
        $orderAction->time = DateTimeHelper::getFormatDateTime(time());

        $orderAction->save();
    }

    public function update($event) {
        $changedAttributes = $event->changedAttributes;
        $orderInfo = $this->owner;
        foreach ($changedAttributes as $key => $value) {
            if (in_array($key, [
                    'user_id',
                    'consignee',
                    'mobile',
                    'province',
                    'city',
                    'districe',
                    'address',
            ]) || !empty($orderInfo->note)) {
                $orderAction = new OrderModifyAction();
                if ($orderInfo instanceof OrderGroup) {
                    $orderAction->group_id = $orderInfo['id'];
                }
                elseif ($orderInfo instanceof OrderInfo) {
                    $orderAction->order_id = $orderInfo['order_id'];
                }

                if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
                    $orderAction->action_user = Yii::$app->user->identity['user_name'];
                }
                else {
                    $orderAction->action_user = '未知用户(可能是批量修改的脚本修改了状态)';
                }

                $orderAction->user_id = $orderInfo['user_id'];
                $orderAction->consignee = $orderInfo['consignee'];
                $orderAction->mobile = $orderInfo['mobile'];
                $orderAction->province = $orderInfo['province'];
                $orderAction->city = $orderInfo['city'];
                $orderAction->district = $orderInfo['district'];
                $orderAction->address = $orderInfo['address'];
                $orderAction->time = DateTimeHelper::getFormatDateTime(time());

                $orderAction->save();
                break;
            }
        }
    }
}