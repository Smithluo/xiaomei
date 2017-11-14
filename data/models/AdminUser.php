<?php

namespace data\models;

use Yii;

/**
 * This is the model class for table "{{%o_admin_user}}".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $email
 * @property string $password
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
class AdminUser extends \yii\db\ActiveRecord
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
}
