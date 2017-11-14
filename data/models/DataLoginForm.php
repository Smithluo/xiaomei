<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/8/18
 * Time: 9:34
 */

namespace data\models;

use common\models\LoginForm;
use common\models\Users;
use Yii;
use yii\web\ForbiddenHttpException;

class DataLoginForm extends LoginForm
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
            $this->_user = Users::findOne(['user_name' => $this->username]);
            if (!$this->_user) {
                $this->_user = Users::findOne(['mobile_phone' => $this->username]);
            }
        }

        //权限认证
        if (!empty($this->_user) && !Yii::$app->authManager->checkAccess($this->_user->user_id, '/data-user/auth-user')) {
            Yii::warning("$this->username 尝试登录，缺少权限", __METHOD__);
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'), 403);            
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
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }
}