<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 20:58
 */

namespace common\behaviors;

use Yii;
use common\helper\DateTimeHelper;
use common\models\GoodsAction;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class RecordCheckUserBehavior extends Behavior
{
    private $lock = false;

    private $oldCheckNote = '';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'before',
            ActiveRecord::EVENT_AFTER_UPDATE => 'after',
        ];
    }

    public function before($event) {

        if ($this->lock) {
            return;
        }

        $userModel = $this->owner;
        $this->oldCheckNote = $userModel->getOldAttribute('checked_note');

        if (empty($userModel->checked_note)) {
            $userModel->checked_note = $this->oldCheckNote;
            $this->lock = true;
        }
    }

    public function after() {
        if ($this->lock) {
            return;
        }
        $this->lock = true;

        $userModel = $this->owner;

        if (isset(Yii::$app->user) && !empty(Yii::$app->user->identity['user_name'])) {
            $actionUserName = Yii::$app->user->identity['user_name']. '('. Yii::$app->user->identity['mobile_phone']. ')';
        }
        else {
            $actionUserName = '未知用户(可能是批量修改的脚本修改了状态)';
        }

        $actionUserName = $actionUserName. '('. DateTimeHelper::getFormatDateTimeNow().')';

        if (!isset($userModel->checked_note)) {
            $userModel->checked_note = '';
        }

        $checkNote = str_replace(trim($this->oldCheckNote), '', $userModel->checked_note);
        if (empty($checkNote)) {
            return;
        }

        $newCheckNote = $actionUserName. ':'. $checkNote;

        $userModel->checked_note = $newCheckNote. PHP_EOL. $this->oldCheckNote;

        $userModel->save();
    }
}