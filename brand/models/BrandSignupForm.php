<?php
namespace brand\models;

use yii\base\ErrorException;
use yii\base\Model;
use common\models\BrandUser;

/**
 * Signup form
 */
class BrandSignupForm extends Model
{
    public $username;
    public $company_name;
    public $password;
    public $status;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\brand\models\BrandUser', 'message' => '用户名已被注册'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['company_name', 'filter', 'filter' => 'trim'],
            ['company_name', 'string', 'max' => 255],
            ['company_name', 'unique', 'targetClass' => '\brand\models\BrandUser', 'message' => '店铺名称已存在，请修改'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new BrandUser();
        $user->username = $this->username;
        $user->company_name = $this->company_name;
        $user->status = 0;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $now = date('Y-m-d H:i:s');
        $user->created_at = $now;
        $user->updated_at = $now;

        return $user->save() ? $user : null;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = BrandUser::findByUsername($this->username);
        }

        return $this->_user;
    }
}
