<?php

namespace common\models\dashboard;

use Yii;

/**
 * This is the model class for table "oa_mark".
 *
 * @property integer $id
 * @property string $date
 * @property integer $user_id
 * @property string $plat_form
 * @property integer $login_times
 * @property integer $click_times
 * @property integer $order_count
 * @property integer $pay_count
 */
class Mark extends \yii\db\ActiveRecord
{
    public static $platFormMap = [
        'm'         => '微信站',
        'pc'        => 'PC站',
//        'ios'       => '苹果APP',
//        'android'   => '安卓APP',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oa_mark';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dboa');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['user_id'], 'required'],
            [['plat_form'], 'string'],
            [['user_id', 'login_times', 'click_times', 'order_count', 'pay_count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => '日期',
            'user_id' => '用户ID',
            'plat_form' => '平台',
            'login_times' => '成功登录次数',
            'click_times' => '浏览页面数量',
            'order_count' => '下单数量',
            'pay_count' => '支付下单数量',
        ];
    }

    /**
     * @inheritdoc
     * @return MarkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MarkQuery(get_called_class());
    }
}
