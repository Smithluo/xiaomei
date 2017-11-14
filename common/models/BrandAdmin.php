<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_admin".
 *
 * @property integer $id
 * @property string $linkman
 * @property string $mobile
 * @property string $address
 */
class BrandAdmin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['linkman', 'mobile'], 'required'],
            [['linkman'], 'string', 'max' => 40],
            [['mobile'], 'string', 'max' => 20],
            [['back_address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'linkman' => '品牌对接人',
            'mobile' => '对接人电话',
            'back_address' => '退货地址',
        ];
    }
}
