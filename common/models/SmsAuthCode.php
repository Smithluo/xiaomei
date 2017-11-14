<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;

/**
 * This is the model class for table "o_sms_auth_code".
 *
 * @property string $mobile
 * @property string $code
 * @property integer $created_at
 * @property integer $expired
 */
class SmsAuthCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_sms_auth_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'code', 'expired'], 'required'],
            [['created_at', 'expired'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['code'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'code' => '验证码',
            'created_at' => '创建时间',
            'expired' => '有效期',
        ];
    }

    /**
     * 检验 手机号对应的验证码是否有效
     *  存在未过期的验证码记录 返回1 ，否则 返回 0
     * @param $mobile
     * @param $code
     * @return bool
     */
    public static function check($mobile, $code)
    {
    	$gmt = DateTimeHelper::gmtime();
        $record = self::find()
            ->where([
                'mobile' => $mobile,
                'code' => $code
            ])->andWhere(['>', 'expired', $gmt])
            ->one();

    	if (!empty($record)) {
    	    return 1;
        } else {
    	    return 0;
        }
    }

    /**
     * @param string $mobile   接验证码的手机号
     * @param string $code     验证码
     * @param int $expired  验证码过期时间，默认session的有效时间
     * @return bool
     */
    public static function uniqueSave($mobile, $code, $expired = 1440)
    {
        $record = self::find()->where(['mobile' => $mobile])->one();

        if (empty($record)) {
            $record = new SmsAuthCode();
        }

        $record->mobile = $mobile;
        $record->created_at = DateTimeHelper::gmtime();
        $record->code = $code;
        $record->expired = $expired + DateTimeHelper::gmtime();

        if ($record->save()) {
            return true;
        } else {
            Yii::warning('验证码入库失败 $mobile = '.$mobile.', $code = '.$code.', $expired = '.$expired, __METHOD__);
            return false;
        }
    }
}
