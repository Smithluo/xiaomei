<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17 0017
 * Time: 16:09
 */

namespace data\models;


use yii\db\ActiveRecord;

class SkuAnalysis extends ActiveRecord
{
    public static function tableName()
    {
        return 'o_analysis_sku';
    }

    public function attributes()
    {
        return ['id', 'goods_id', 'date'];
    }

    public function rules()
    {
        return [
            [['id', 'goods_id', 'date'], 'safe']
        ];
    }
}