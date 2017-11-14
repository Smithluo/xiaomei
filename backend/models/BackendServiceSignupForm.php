<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/1 0001
 * Time: 16:03
 */

namespace backend\models;


use service\models\SignupForm;

class BackendServiceSignupForm extends SignupForm
{

    public $id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\service\models\ServiceUser', 'message' => '这个用户名已经被注册，请重新输入。'],
            ['username', 'string', 'min' => 2, 'max' => 255],

//            ['email', 'filter', 'filter' => 'trim'],
//            ['email', 'required'],
//            ['email', 'email'],
//            ['email', 'string', 'max' => 255],
//            ['email', 'unique', 'targetClass' => '\service\models\ServiceUser', 'message' => '这个email已经被注册，请重新输入。'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['confirm_password', 'required'],
            ['confirm_password', 'string', 'min' => 6],

            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>'密码与确认密码不相等，请重新输入。'],
        ];
    }
}