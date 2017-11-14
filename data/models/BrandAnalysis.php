<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17 0017
 * Time: 16:07
 */

namespace data\models;


use yii\db\ActiveRecord;

class BrandAnalysis extends ActiveRecord
{
    public static function tableName()
    {
        return 'o_analysis_brand';
    }

    public function attributes()
    {
        return ['id', 'brand_id', 'date'];
    }

    public function rules()
    {
        return [
            [['id', 'brand_id', 'date'], 'safe']
        ];
    }
}