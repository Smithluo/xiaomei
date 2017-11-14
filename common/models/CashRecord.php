<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "o_cash_record".
 *
 * @property integer $id
 * @property string $cash
 * @property string $user_id
 * @property string $note
 * @property string $pay_time
 * @property string $created_time
 * @property string $balance
 */
class CashRecord extends \yii\db\ActiveRecord
{
    const CASH_RECORD_TYPE_IN = 1;  //  入账
    const CASH_RECORD_TYPE_OUT = 2; //  出账

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_cash_record';
    }

    /**
     * 计算余额
     * @return int|string
     */
    public static function totalCash($user_id = 0) {
        if($user_id === 0) {
            $user_id = Yii::$app->user->identity['user_id'];
        }
        
        $rs = CashRecord::find()
            ->select('balance')
            ->andWhere(['user_id'=>$user_id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return $rs ? $rs->balance : 0;
    }

    /**
     * 计算入账金额
     * @return int|string
     */
    public static function totalInCash($user_id = null) {
        $result = CashRecord::find()->andWhere(['user_id'=>Yii::$app->user->identity['user_id']])
            ->andWhere(['>', 'cash', 0])
            ->sum('cash');
        return $result;
    }

    /**
     * 计算一个月内的入账金额
     * @return mixed
     */
    public static function monthTotalInCash() {
        $result = CashRecord::find()->andWhere(['user_id'=>Yii::$app->user->identity['user_id']])
            ->andWhere(['>', 'cash', 0])
            ->andWhere(['>', 'created_time', DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60)])
            ->sum('cash');
        return $result;
    }

    /**
     * 计算出账金额
     * @return int|string
     */
    public static function totalOutCash($user_id = null) {
        $result = CashRecord::find()->andWhere(['user_id'=>Yii::$app->user->identity['user_id']])
            ->andWhere(['<', 'cash', 0])
            ->sum('cash');
        return abs($result);
    }

    /**
     * 计算一个月内的出账金额
     * @return mixed
     */
    public static function monthTotalOutCash() {
        $result = CashRecord::find()->andWhere(['user_id'=>Yii::$app->user->identity['user_id']])
            ->andWhere(['<', 'cash', 0])
            ->andWhere(['>', 'created_time', DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60)])
            ->sum('cash');
        return $result;
    }

    /**
     * 批量获取可提取总额
     * @param $user_ids
     * @return array
     */
    public static function totalCashList($user_ids) {
        if(!is_array($user_ids)) {
            return [];
        }
        /*2016-12-27 修改这个查询方法时 只有一处调用 service\controllers\ServiceSiteController.php 的 actionIndex()
         * 这样查询多个用户只产生一条记录，$user_ids 的最后一个id有值，前面的业务员 提成余额为0
        $query = new Query();
        $result = $query->select(['user_id', 'SUM(cash) as total_cash'])
            ->from(CashRecord::tableName())
            ->where(['user_id' => $user_ids])
            ->all();*/

        $result = CashRecord::find()->select(['user_id', 'SUM(cash) as total_cash'])
            ->where(['user_id' => $user_ids])
            ->groupBy('user_id')
            ->asArray()
            ->all();

        return $result;
    }

    /**
     * 修正 指定cashRecord id的 balance
     * @return int|string
     */
    public static function getBalanceById($userId, $id)
    {
        return CashRecord::find()
            ->andWhere(['user_id' => $userId])
            ->andWhere(['<=', 'id', $id])
            ->sum('cash');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cash', 'balance'], 'number'],
            [['user_id'], 'integer'],
            [['pay_time', 'created_time'], 'safe'],
            [['note'], 'string', 'max' => 65535],
            ['note', 'default', 'value' => ' '],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cash' => '金额',
            'user_id' => '用户id',
            'note' => '备注',
            'pay_time' => '汇款时间',
            'created_time' => '入账时间',
            'balance' => '余额',
        ];
    }

    public function getUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return CashRecordQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CashRecordQuery(get_called_class());
    }

    public static function createFromServicerDivideRecord($divideRecord) {
        if ($divideRecord->money_in_record_id > 0) {
            Yii::warning('已经分成过了', __METHOD__);
            return false;
        }

        //创建服务商的钱包流水
        $cashRecord = new CashRecord();
        $cashRecord->cash = $divideRecord->divide_amount + $divideRecord->parent_divide_amount;
        if (!empty($divideRecord->parent_servicer_user_id)) {
            $cashRecord->user_id = $divideRecord->parent_servicer_user_id;
        }
        else {
            $cashRecord->user_id = $divideRecord->servicer_user_id;
        }

        $cashRecord->note = $divideRecord->group_id;
        $cashRecord->created_time = DateTimeHelper::getFormatGMTDateTime();

        $totalCash = CashRecord::totalCash($cashRecord->user_id);
        $totalCash = empty($totalCash) ? 0.00 : $totalCash;
        $cashRecord->balance = $totalCash + $cashRecord->cash;

        return $cashRecord;
    }

    public static function createSalemanCashRecord($divideRecord) {
        if ($divideRecord->money_in_record_id > 0) {
            Yii::warning('已经分成过了', __METHOD__);
            return false;
        }

        if (empty($divideRecord)) {
            return false;
        }

        if ($divideRecord['divide_amount'] > -0.001 && $divideRecord['divide_amount'] < 0.001) {
            Yii::warning('金额不足，不用入账', __METHOD__);
            return false;
        }

        if (CashRecord::find()->where([
            'note' => $divideRecord->group_id,
            'user_id' => $divideRecord->servicer_user_id,
        ])->exists()) {
            Yii::warning('业务员已经提成过了', __METHOD__);
            return false;
        }

        if ($divideRecord->servicer_user_id == $divideRecord->parent_servicer_user_id) {
            Yii::warning('业务员和服务商是同一个用户', __METHOD__);
            return false;
        }

        $roles = Yii::$app->authManager->getRolesByUser($divideRecord->servicer_user_id);
        if (isset($roles['service_boss'])) {
            Yii::warning('用户是服务商，不用重复入账', __METHOD__);
            return false;
        }

        $cashRecord = new CashRecord();
        $cashRecord->cash = $divideRecord->divide_amount;
        $cashRecord->user_id = $divideRecord->servicer_user_id;
        $cashRecord->note = $divideRecord->group_id;
        $cashRecord->created_time = DateTimeHelper::getFormatGMTDateTime();

        $totalCash = CashRecord::totalCash($cashRecord->user_id);
        $totalCash = empty($totalCash) ? 0.00 : $totalCash;
        $cashRecord->balance = $totalCash + $cashRecord->cash;

        return $cashRecord;
    }
}
