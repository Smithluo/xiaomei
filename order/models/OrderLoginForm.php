<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17 0017
 * Time: 18:15
 */

namespace order\models;

use Yii;
use common\models\LoginForm;
use common\models\OrderUser;

class OrderLoginForm extends LoginForm
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
            $this->_user = OrderUser::findOne(['user_name' => $this->username]);
            if (empty($this->_user)) {
                $this->_user = OrderUser::findOne([
                    'mobile_phone' => $this->username,
                ]);
            }

            //  如果账号验证通过，验证用户的基本权限
//            if (!empty($this->_user) && Yii::$app->user->can('/order-site/index'))
            if (
                !empty($this->_user) &&
                !Yii::$app->authManager->checkAccess($this->_user->user_id, '3rd_order_import')
            ) {
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
                $this->addError($attribute, '用户名或密码错误，或者当前账号没有操作权限');
            }
        }
    }
}