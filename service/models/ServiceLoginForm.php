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

class ServiceLoginForm extends LoginForm
{
    private $_user;

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = ServiceUser::findOne(['user_name' => $this->username]);
            if (empty($this->_user)) {
                $this->_user = ServiceUser::findOne([
                    'mobile_phone' => $this->username,
                ]);
            }

            if (!empty($this->_user)
                && !Yii::$app->authManager->checkAccess($this->_user->user_id, 'service_boss')
                && !Yii::$app->authManager->checkAccess($this->_user->user_id, 'service_saleman')
                && !Yii::$app->authManager->checkAccess($this->_user->user_id, 'service_manager')) {
                Yii::warning("$this->username 尝试登录，缺少权限", __METHOD__);
                $this->_user = null;
            }
        }

        return $this->_user;
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
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误，或者当前账号不是服务商');
            }
        }
    }
}