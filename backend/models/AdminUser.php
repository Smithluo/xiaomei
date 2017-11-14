<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%o_admin_user}}".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property string $ec_salt
 * @property integer $add_time
 * @property integer $last_login
 * @property string $last_ip
 * @property string $action_list
 * @property string $touch_action_list
 * @property string $nav_list
 * @property string $lang_type
 * @property integer $agency_id
 * @property integer $suppliers_id
 * @property string $todolist
 * @property integer $role_id
 */
class AdminUser extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%o_admin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time', 'last_login', 'agency_id', 'suppliers_id', 'role_id'], 'integer'],
            [['action_list', 'nav_list', 'agency_id'], 'required'],
            [['action_list', 'touch_action_list', 'nav_list', 'todolist'], 'string'],
            [['user_name', 'email'], 'string', 'max' => 60],
            [['password'], 'string', 'max' => 32],
            [['ec_salt'], 'string', 'max' => 10],
            [['last_ip'], 'string', 'max' => 15],
            [['lang_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'email' => 'Email',
            'password' => 'Password',
            'ec_salt' => 'Ec Salt',
            'add_time' => 'Add Time',
            'last_login' => 'Last Login',
            'last_ip' => 'Last Ip',
            'action_list' => 'Action List',
            'touch_action_list' => '微信站权限',
            'nav_list' => 'Nav List',
            'lang_type' => 'Lang Type',
            'agency_id' => 'Agency ID',
            'suppliers_id' => 'Suppliers ID',
            'todolist' => 'Todolist',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * @inheritdoc
     * @return AdminUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdminUserQuery(get_called_class());
    }

    /**
     *  编译密码函数
     *
     * @access  public
     * @param   array   $cfg 包含参数为 $password, $md5password, $salt, $type
     *
     * @return void
     */
    private function compile_password ($cfg)
    {
        if (isset($cfg['password']))
        {
            $cfg['md5password'] = md5($cfg['password']);
        }
        if (empty($cfg['type']))
        {
            $cfg['type'] = Yii::$app->params['PWD_MD5'];
        }

        switch ($cfg['type'])
        {
            case Yii::$app->params['PWD_MD5'] :
                if(!empty($cfg['ec_salt']))
                {
                    return md5($cfg['md5password'].$cfg['ec_salt']);
                }
                else
                {
                    return $cfg['md5password'];
                }

            case Yii::$app->params['PWD_PRE_SALT'] :
                if (empty($cfg['salt']))
                {
                    $cfg['salt'] = '';
                }

                return md5($cfg['salt'] . $cfg['md5password']);

            case Yii::$app->params['PWD_SUF_SALT'] :
                if (empty($cfg['salt']))
                {
                    $cfg['salt'] = '';
                }

                return md5($cfg['md5password'] . $cfg['salt']);

            default:
                return '';
        }
    }

    public function validatePassword($password) {

        if ($this->password != $this->compile_password(array('password'=>$password,'ec_salt'=>$this->ec_salt)))
        {
            return false;
        }
        else
        {
            //未加盐就加盐
            if(empty($this->ec_salt))
            {
                $ec_salt=rand(1,9999);
                $new_password=md5(md5($password).$ec_salt);
                //注意这里是用user_name加盐
//                $sql = "UPDATE ".$this->table($this->user_table)."SET password= '" .$new_password."',ec_salt='".$ec_salt."'".
//                    " WHERE user_name='$post_username'";
//                $this->db->query($sql);
                $this->password = $new_password;
                $this->ec_salt = $ec_salt;
                $this->update();
            }
            return true;
        }

    }


    public static function findIdentity($id) {
        return static::findOne(['user_id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
//        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        return static::findOne(['access_token' => $token]);
    }

    public function getId() {
        return $this->user_id;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }
}
