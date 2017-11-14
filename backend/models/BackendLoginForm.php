<?php
namespace backend\models;

use common\models\LoginForm;
use common\models\Users;
use Yii;

/**
 * Login form
 */
class BackendLoginForm extends LoginForm
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
            $this->_user = Users::findOne(['user_name'=>$this->username]);

            if (empty($this->_user)) {
                $this->_user = Users::findOne([
                    'mobile_phone' => $this->username,
                ]);
            }

            if (!empty($this->_user) && !Yii::$app->authManager->checkAccess($this->_user->user_id, '/site/index')) {
                Yii::warning("$this->username 尝试登录，缺少权限", __METHOD__);
                $this->_user = null;
            }
        }

        return $this->_user;
    }
}
