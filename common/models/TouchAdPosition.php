<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_touch_ad_position".
 *
 * @property integer $position_id
 * @property string $position_name
 * @property integer $ad_width
 * @property integer $ad_height
 * @property string $position_desc
 * @property string $position_style
 */
class TouchAdPosition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_touch_ad_position';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_width', 'ad_height'], 'integer'],
//            [['position_style'], 'required'],
            [['position_style'], 'string'],
            [['position_name'], 'string', 'max' => 60],
            [['position_desc'], 'string', 'max' => 255],
            [['position_style', 'position_name'], 'required', ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'position_id' => 'ID',
            'position_name' => '广告位名称',
            'ad_width' => '宽度',
            'ad_height' => '高度',
            'position_desc' => '描述',
            'position_style' => '模版代码',
        ];
    }

    public static function getTouchAdPositionList() {

        $result = [];
        $list = self::find()->asArray()->all();
        if (!empty($list)) {
            $result = ArrayHelper::map($list,"position_id","position_name");
        }
        return $result;

    }
}
