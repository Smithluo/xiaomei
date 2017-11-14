<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17 0017
 * Time: 18:15
 */

namespace service\models;

use Yii;
use common\models\LoginForm;
use common\models\ServiceUser;
use common\models\Users;

class AdminServiceLoginForm extends LoginForm
{
    private $_user;
    public $servicerUserId;

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['servicerUserId', 'safe'],
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findOne([
                'user_name' => $this->username,
            ]);
            if (empty($this->_user)) {
                $this->_user = Users::findOne([
                    'mobile_phone' => $this->username,
                ]);
            }
        }

        return $this->_user;
    }

    public function login()
    {
        if ($this->validate()) {
            $user = Users::findOne([
                'user_id' => $this->servicerUserId,
            ]);
            return Yii::$app->user->login($user, 0);
        } else {
            return false;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user ||
                !in_array($user->mobile_phone, [
                    '13510601717',
                    '13077807890',
                ]) ||
                !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误，或者当前账号不是服务商');
            }
        }
    }
}