<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_pkg".
 *
 * @property integer $pkg_id
 * @property string $pkg_name
 * @property string $allow_goods_list
 * @property string $deny_goods_list
 * @property integer $updated_at
 */
class GoodsPkg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_pkg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pkg_name', 'updated_at'], 'required'],
            [['allow_goods_list', 'deny_goods_list'], 'string'],
            [['updated_at'], 'integer'],
            [['pkg_name'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pkg_id' => '商品包ID',
            'pkg_name' => '商品包名称',
            'allow_goods_list' => '商品包支持的范围',
            'deny_goods_list' => '商品包不支持的范围',
            'updated_at' => '创建时间',
        ];
    }
    
}
