<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_app_ad_position".
 *
 * @property integer $id
 * @property string $title
 */
class AppAdPosition extends \yii\db\ActiveRecord
{
    static private $adPositionMap = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_app_ad_position';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
        ];
    }

    public static function getAllAdPositionMap() {
        if (!isset(self::$adPositionMap)) {
            $list = self::find()->asArray()->indexBy('id')->all();
            self::$adPositionMap = ArrayHelper::getColumn($list, 'title', true);
        }
        return self::$adPositionMap;
    }
}
