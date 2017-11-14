<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_arrival_reminder".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $goods_id
 * @property string $add_time
 * @property integer $status
 */
class ArrivalReminder extends \yii\db\ActiveRecord
{
    const NOT_ARRIVAL = 0;
    const HAS_ARRIVAL = 1;

    public static $arrival_map = [
        self::NOT_ARRIVAL => '尚未到货',
        self::HAS_ARRIVAL => '已经到货',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_arrival_reminder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id', 'add_time', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'goods_id' => '商品',
            'add_time' => '添加时间',
            'status' => '状态',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), [ 'goods_id' => 'goods_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * @param $userId
     * @param $goodsId
     * @return array
     * 设置到货提醒
     */
    public static function addArrivalReminder($userId, $goodsId)
    {
        $reminder = self::find()->where(['user_id' => $userId, 'goods_id' => $goodsId])->one();

        $goods = Goods::find()->select(['goods_number', 'start_num'])->where(['goods_id' => $goodsId])->one();

        if($goods->goods_number > $goods->start_num) {
            return [
                'code' => '3',
                'msg' => '该商品已到货，请刷新购买',
            ];
        }

        if($reminder) {
            $reminder->status = self::NOT_ARRIVAL;
            if($reminder->save()) {
                return [
                    'code' => '0',
                    'msg' => '提醒到货成功'
                ];
            } else {
                Yii::warning(__METHOD__.'到货提醒存储失败，原因为:'.VarDumper::export($reminder->getErrors()));
                return [
                    'code' => '2',
                    'msg' => '失败'
                ];
            }
        } else {
            $arrival = new ArrivalReminder();
            $arrival->user_id = $userId;
            $arrival->goods_id = $goodsId;
            $arrival->add_time = DateTimeHelper::gmtime();
            $arrival->status = self::NOT_ARRIVAL;

            if($arrival->save()) {
                return [
                    'code' => '0',
                    'msg' => '提醒到货成功'
                ];
            } else {
                Yii::warning(__METHOD__.'到货提醒存储失败，原因为:'.VarDumper::export($arrival->getErrors()));
                return [
                    'code' => '2',
                    'msg' => '失败'
                ];
            }
        }
    }
}
