<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/20
 * Time: 16:40
 */

namespace backend\components;

use backend\models\Event;
use common\models\AuthAssignment;
use Yii;
use \yii\rbac\Rule;

class EventRule extends Rule
{
    public $name = 'event';

    public function execute($user, $item, $params)
    {
        $eventType = isset($params['event_type']) ? $params['event_type'] : 0;

        if ($eventType == Event::EVENT_TYPE_COUPON) {
            //  判断创建人是不是 管理员，  $userId = $params['post']->updated_by;
            $item =  AuthAssignment::find()->where(['user_id' => $user])->one();
            if ($user) {
                if (!empty($item) && $item->item_name == '管理员') {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }

        // TODO: Implement execute() method.
    }
}