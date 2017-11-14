<?php

namespace home\models;

use Yii;

/**
 * This is the model class for table "o_brand_application".
 *
 * @property integer $id
 * @property string $company_name
 * @property string $company_address
 * @property string $name
 * @property string $position
 * @property string $contact
 * @property string $brands
 * @property string $licence
 * @property string $recorded
 * @property string $registed
 * @property string $taxed
 * @property string $checked
 */
class BrandApplication extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_application';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['licence', 'recorded', 'registed', 'taxed', 'checked'], 'string', 'max' => 4],
            [['company_name', 'company_address', 'brands'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 20],
            [['position', 'contact'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_name' => '贵公司名称',
            'company_address' => '贵公司地址',
            'name' => '您的姓名',
            'position' => '您的职务',
            'contact' => '您的联系方式',
            'brands' => '品牌名称',
            'licence' => '是否有品牌代理销售授权书',
            'recorded' => '是否完成产品药监局备案',
            'registed' => '能否提供商标注册证明',
            'taxed' => '能否提供报关单及完税证明',
            'checked' => '能否提供检疫检验证明',
        ];
    }
}
