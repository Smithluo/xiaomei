<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_shop_config".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $code
 * @property string $type
 * @property string $store_range
 * @property string $store_dir
 * @property string $value
 * @property integer $sort_order
 */
class ShopConfig extends \yii\db\ActiveRecord
{
    const TYPE_SHOP_INFO        = 1;
    const TYPE_BASIC            = 2;
    const TYPE_DISPLAY          = 3;
    const TYPE_SHOPPING_FLOW    = 4;
    const TYPE_SMTP             = 5;
    const TYPE_HIDDEN           = 6;
    const TYPE_GOODS            = 7;
    const TYPE_SMS              = 8;
    const TYPE_WAP              = 9;
    const TYPE_WECHAT_ACC       = 10;
    const TYPE_WECHAT_SHARE     = 11;
    const TYPE_TPL_MSG_CONFIG   = 12;

    //减库存时机
    const SDT_SHIP = 0;         //发货时
    const SDT_PLACE = 1;        //下单时
    const SDT_PAID = 2;         //付款后

    public static $parent_map = [
        self::TYPE_SHOP_INFO        => '网店信息',
        self::TYPE_BASIC            => '基本设置',
        self::TYPE_DISPLAY          => '显示设置',
        self::TYPE_SHOPPING_FLOW    => '采购流程',
        self::TYPE_SMTP             => '邮件服务器设置',
        self::TYPE_HIDDEN           => 'EC隐藏配置',
        self::TYPE_GOODS            => '商品显示设置',
        self::TYPE_SMS              => '短信设置',
        self::TYPE_WAP              => 'WAP设置',
        self::TYPE_WECHAT_ACC       => 'wechat_token',
        self::TYPE_WECHAT_SHARE     => 'wechat_share',
        self::TYPE_TPL_MSG_CONFIG   => 'wechat_tpl',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_shop_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort_order'], 'integer'],
            [['value'], 'required'],
            [['value'], 'string'],
            [['code'], 'string', 'max' => 30],
            [['type'], 'string', 'max' => 10],
            [['store_range', 'store_dir'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '配置ID',
            'parent_id' => '所属组ID',
            'code' => '配置代码',
            'type' => '配置类型',
            'store_range' => '存储类型',
            'store_dir' => 'Store Dir',
            'value' => '值',
            'sort_order' => '排序值（逆序，0～255）',
        ];
    }

    /**
     * 获取配置项的值
     * @param $code
     */
    public static function getConfigValue($code)
    {
        $rs = self::findOne(['code' => $code]);

        return $rs->value;
    }


    public static function getStorageTypeMap()
    {
        $storage_map = [];
        $rs = self::find()->select('type')->distinct('type')->asArray()->all();
        if ($rs) {
            foreach ($rs as $item) {
                $storage_map[$item['type']] = $item['type'];
            }
        }

        return $storage_map;
    }

    public static function renewCache()
    {
        $cache_list = [
            'IMG_BASE_URL',
        ];

    }
}
