<?php
namespace brand\models;

use common\models\BrandUser;
use common\models\LoginForm;

/**
 * Login form
 */
class BrandLoginForm extends LoginForm
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
            $this->_user = BrandUser::findOne(['mobile_phone' => $this->username]);
            if (!$this->_user) {

                $this->_user = BrandUser::findOne(['user_name' => $this->username]);
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
                $this->addError($attribute, 'Incorrect user_name or password.');
            }
        }
    }

}
