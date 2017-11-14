<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_ad_position".
 *
 * @property integer $position_id
 * @property string $position_name
 * @property integer $ad_width
 * @property integer $ad_height
 * @property string $position_desc
 * @property string $position_style
 */
class AdPosition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_ad_position';
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
            [['position_name', 'position_style'], 'required'],
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

    /**
     * @param $positionName
     * @return int|mixed
     */
    public static function findPositionByName($positionName)
    {
        $model = self::find()
            ->select(['position_id'])
            ->where(['position_name' => $positionName])
            ->one();

        if (!empty($model)) {
            $positionId = $model->position_id;
        } else {
            $positionId = 0;
        }

        return $positionId;
    }

    public static function getAdPositionList() {

        $result = [];
        $list = self::find()->asArray()->all();
        if (!empty($list)) {
            $result = ArrayHelper::map($list,"position_id","position_name");
        }
        return $result;

    }
}
