<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "o_goods_lock_stock".
 *
 * @property integer $id
 * @property string $goods_id
 * @property string $user_id
 * @property integer $enable
 * @property integer $lock_num
 * @property string $lock_time
 * @property string $expired_time
 * @property string $note
 */
class GoodsLockStock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_lock_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'user_id', 'enable', 'lock_num', 'lock_time', 'expired_time'], 'integer'],
            [['note'], 'string'],
            [['note'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品',
            'user_id' => '操作者',
            'enable' => '是否可用',
            'lock_num' => '锁定数量',
            'lock_time' => '操作锁定的时间',
            'expired_time' => '自动解锁时间',
            'note' => '锁定备注',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getUser() {
        return $this->hasOne(Users::className(), [
            'user_id' => 'user_id',
        ]);
    }

    //释放已锁定的库存
    public function release() {
        Yii::$app->getDb()->createCommand()->setSql('lock tables '. self::tableName(). ' WRITE')->execute();
        $transaction = self::getDb()->beginTransaction();
        try {
            $goods = $this->goods;
            //商品已经不存在了，这个锁也没存在必要了
            if (empty($goods)) {
                $this->delete();
                $transaction->commit();
                Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
                return;
            }

            //把库存加回去
            $goods->goods_number += $this->lock_num;
            if (!$goods->save()) {
                throw new Exception('保存商品失败');
            }

            $this->delete();
            $transaction->commit();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
        }

    }
}
