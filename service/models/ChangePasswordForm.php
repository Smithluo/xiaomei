<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16 0016
 * Time: 9:20
 */

namespace service\models;


use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $password_old;
    public $password;
    public $password_repeat;

    public function attributeLabels()
    {
        return [
            'password_old' => '原始密码',
            'password' => '新密码',
            'password_repeat' => '确认新密码',
        ]; // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password_old', 'password', 'password_repeat'] , 'required'],
            ['password', 'string', 'max' => 16],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message'=>'密码与重复输入密码不相等，请重新输入'],
        ];
    }


}