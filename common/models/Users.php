<?php

namespace common\models;

use common\helper\CacheHelper;
use common\behaviors\RecordCheckUserBehavior;
use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use common\models\OrderInfo;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "o_users".
 *
 * @property string $user_id
 * @property string $email
 * @property string $user_name
 * @property string $password
 * @property string $question
 * @property string $answer
 * @property integer $sex
 * @property string $birthday
 * @property string $user_money
 * @property string $frozen_money
 * @property string $pay_points
 * @property string $rank_points
 * @property string $address_id
 * @property string $zone_id
 * @property string $reg_time
 * @property string $last_login
 * @property string $last_time
 * @property string $last_ip
 * @property integer $visit_count
 * @property integer $user_rank
 * @property integer $is_special
 * @property string $ec_salt
 * @property string $salt
 * @property integer $parent_id
 * @property integer $flag
 * @property string $alias
 * @property string $msn
 * @property string $qq
 * @property string $office_phone
 * @property string $home_phone
 * @property string $mobile_phone
 * @property string $company_name
 * @property integer $is_validated
 * @property string $credit_line
 * @property string $passwd_question
 * @property string $passwd_answer
 * @property string $headimgurl
 * @property string $openid
 * @property string $qq_open_id
 * @property string $aite_id
 * @property string $unionid
 * @property string $wx_pc_openid
 * @property string $licence_image
 * @property string $servicer_user_id
 * @property string $servicer_super_id
 * @property string $brand_id_list
 * @property string $auth_key
 * @property string $access_token
 * @property string $servicer_info_id
 * @property string $nickname
 * @property string $bank_info_id
 * @property string $checked_note
 * @property integer $is_checked
 * @property integer $user_type
 * @property string $shopfront_pic
 * @property string $biz_license_pic
 * @property string $channel
 * @property integer $province
 * @property integer $city
 * @property string $token_expired
 * @property integer $int_balance
 * @property string $divide_percent
 * @property string $user_check_note
 * @property integer $recommend_id
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    //  用户审核状态
    const IS_CHECKED_STATUS_IN_REVIEW = 0;
    const IS_CHECKED_STATUS_REFUSED = 1;
    const IS_CHECKED_STATUS_PASSED = 2;
    const IS_CHECKED_STATUS_BLACK = 3;
    //  用户等级
    const USER_RANK_REGISTED = 1;
    const USER_RANK_MEMBER = 2;
    const USER_RANK_VIP = 3;
    //  用户类别
    const USER_TYPE_SHOP        = 1;
    const USER_TYPE_E_COM       = 2;
    const USER_TYPE_WECHAT_BIZ  = 3;
    const USER_TYPE_AGENT       = 4;
    const USER_TYPE_SUPPLIER    = 5;
    const USER_TYPE_SERVICER    = 6;
    const USER_TYPE_OTHER       = 7;

    //  用户审核状态
    public static $is_checked_map = [
        self::IS_CHECKED_STATUS_IN_REVIEW   => '未审核',
        self::IS_CHECKED_STATUS_REFUSED     => '拒绝',
        self::IS_CHECKED_STATUS_PASSED      => '通过',
        self::IS_CHECKED_STATUS_BLACK      => '拉黑',
    ];
    //  用户审核状态图标
    public static $is_checked_img_map = [
        self::IS_CHECKED_STATUS_IN_REVIEW   => '<span class="glyphicon glyphicon-question-sign"> 未审核 </span>',
        self::IS_CHECKED_STATUS_REFUSED     => '<span class="glyphicon glyphicon-remove"> 拒绝 </span>',
        self::IS_CHECKED_STATUS_BLACK     => '<span class="glyphicon glyphicon-remove"> 拉黑 </span>',
        self::IS_CHECKED_STATUS_PASSED      => '<span class="glyphicon glyphicon-ok"> 通过 </span>',
    ];

    //  用户等级
    public static $user_rank_map = [
        self::USER_RANK_REGISTED    => '普通会员', //  待审核
        self::USER_RANK_MEMBER      => 'VIP会员',
        self::USER_RANK_VIP         => 'SVIP会员',
    ];
    //  购买所需用户等级
    public static $need_user_rank_map = [
        self::USER_RANK_REGISTED    => '普通会员', //  待审核
        self::USER_RANK_MEMBER      => 'VIP会员',
        self::USER_RANK_VIP         => 'SVIP会员',
    ];

    //  用户类别
    public static $user_type_map = [
        self::USER_TYPE_SHOP => '美妆店',
        self::USER_TYPE_E_COM => '电商',
        self::USER_TYPE_WECHAT_BIZ => '微商',
        self::USER_TYPE_AGENT => '代理商',
        self::USER_TYPE_SUPPLIER => '品牌商',
        self::USER_TYPE_SERVICER => '服务商',
        self::USER_TYPE_OTHER => '其他',
    ];

    const CHANNEL_TYPE_DISPLAY = 1;
    const CHANNEL_TYPE_FRIENDS = 2;
    const CHANNEL_TYPE_WECHAT = 3;
    const CHANNEL_TYPE_PINGUAN = 4;
    const CHANNEL_TYPE_SERVICE = 5;
    const CHANNEL_TYPE_OTHER = 6;

    public static $channel_map = [
        self::CHANNEL_TYPE_DISPLAY => '展会',
        self::CHANNEL_TYPE_FRIENDS => '朋友介绍',
        self::CHANNEL_TYPE_WECHAT => '微信',
        self::CHANNEL_TYPE_PINGUAN => '品观网',
        self::CHANNEL_TYPE_SERVICE => '服务商',
        self::CHANNEL_TYPE_OTHER => '其他',
    ];

    public static $notice_map = [
        '0' => '请选择',
        '原因：提交的资质照片不符合标准' => '原因：提交的资质照片不符合标准',
        '原因：提交的店铺资料不符合标准' => '原因：提交的店铺资料不符合标准',
        '原因：提交的店铺资料与资质照片不匹配' => '原因：提交的店铺资料与资质照片不匹配',
        '原因：非常抱歉，您的资料未能通过审核。小美诚品专注服务线下实体门店，根据您提交的资料，平台不能为您提供服务。' => '原因：非常抱歉，您的资料未能通过审核。小美诚品专注服务线下实体门店，根据您提交的资料，平台不能为您提供服务。',
        '原因：非常抱歉，由于您长期未在平台登录，您的访问权限已被系统关闭' => '原因：非常抱歉，由于您长期未在平台登录，您的访问权限已被系统关闭',
        '原因：非常抱歉，由于您长期未在平台采购，您的访问权限已被系统关闭' => '原因：非常抱歉，由于您长期未在平台采购，您的访问权限已被系统关闭',
    ];

    public $localPlace;
    public $shippingPlace;
    public $send_sms;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_users';
    }

    public function checkMobile() {
        if ($this->mobile_phone && !TextHelper::isMobile($this->mobile_phone)) {
            $this->addError('mobile_phone', '号码无效，请输入正确的手机号码');
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex', 'pay_points', 'rank_points', 'address_id', 'zone_id', 'reg_time', 'last_login', 'visit_count',
                'user_rank', 'is_special', 'parent_id', 'flag', 'is_validated', 'servicer_user_id', 'servicer_super_id',
                'servicer_info_id', 'servicer_super_id', 'is_checked', 'user_type', 'province', 'city', 'int_balance', 'divide_percent', 'recommend_id'], 'integer'],
            [['shopfront_pic', 'biz_license_pic'], 'default', 'value' => ''],
            [['province', 'city'], 'default', 'value' => 0],
			[['parent_id', 'servicer_super_id', 'bank_info_id', 'recommend_id'], 'default', 'value'=>0],
            [['birthday', 'last_time', 'is_checked', 'checked_note', 'user_type', 'shopfront_pic', 'biz_license_pic', 'province', 'city', 'token_expired', 'regionList', 'user_check_note'], 'safe'],
            [['user_money', 'frozen_money', 'credit_line'], 'number'],
//            [['alias', 'msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone', 'company_name', 'credit_line',
//                'qq_open_id', 'unionid', 'wx_pc_openid', 'licence_image'], 'required'],
            [['user_name', 'password', 'mobile_phone', 'company_name'], 'required'],
            [['alias', 'msn', 'qq', 'office_phone', 'home_phone', 'company_name', 'mobile_phone', 'qq_open_id', 'unionid', 'wx_pc_openid', 'licence_image'], 'default', 'value'=>''],
            [['credit_line', 'int_balance'], 'default', 'value'=>0],
            [['email', 'alias', 'msn', 'channel'], 'string', 'max' => 60],
//            ['email', 'email'],   前端没有校验email格式，导致email格式不对的用户无法审核
            [['user_name', 'question', 'answer', 'company_name', 'passwd_answer', 'headimgurl', 'licence_image',
                'brand_id_list', 'access_token', 'shopfront_pic', 'biz_license_pic'], 'string', 'max' => 255],
            [['nickname'], 'string', 'max' => 255],
            [['password', 'auth_key'], 'string', 'max' => 32],
            [['last_ip'], 'string', 'max' => 15],
            [['ec_salt', 'salt'], 'string', 'max' => 10],
            [['qq', 'office_phone', 'home_phone', 'mobile_phone'], 'string', 'max' => 20],
            [['passwd_question', 'openid', 'qq_open_id', 'aite_id', 'unionid', 'wx_pc_openid'], 'string', 'max' => 50],
            [['checked_note'], 'string'],
            [['user_name'], 'unique', 'message'=>'用户名已被注册，请重新输入。'],
            [['mobile_phone'], 'unique', 'message'=>'手机号已被注册，请重新输入。'],
            [['mobile_phone'], 'checkMobile', 'skipOnEmpty' => false],

            ['user_rank', 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '会员ID',
            'email' => '会员Email',
            'user_name' => '用户名',
            'password' => '密码',
            'question' => '密保问题',   //  未使用到
            'answer' => '密码回答',     //  未使用到
            'sex' => '性别',
            'birthday' => '出生日期',
            'user_money' => '用户余额',
            'frozen_money' => '用户冻结资金',
            'pay_points' => '消费积分',
            'rank_points' => '会员等级积分',
            'address_id' => '默认收货地址ID',
            'zone_id' => '配送区域ID', // 暂时没有用到，可能是配送区域的ID 对应配送站点
            'reg_time' => '注册时间',
            'last_login' => '最近登录',   //  最后一次登录时间
            'last_time' => '最后修改', //  应该是最后一次修改信息时间，该表信息从其他表同步过来考虑
            'last_ip' => '最后一次登录IP',
            'visit_count' => '点击次数',
//            'login_count' => '登录次数',
            'user_rank' => '会员等级',
            'is_special' => '是否特殊会员',
            'ec_salt' => '加密ec_salt',
            'salt' => '加密salt',
            'parent_id' => '推荐人ID',
            'flag' => '标识',
            'alias' => '昵称',
            'msn' => 'msn账号',
            'qq' => 'QQ账号',
            'office_phone' => '办公电话',
            'home_phone' => '家用电话',
            'mobile_phone' => '手机号码',
            'company_name' => '店铺名称',
            'is_validated' => '是否生效',
            'credit_line' => '信用额度',
            'passwd_question' => '密码验证问题', //  未使用到，可能与question重复
            'passwd_answer' => '密码验证答案',     //  未使用到，可能与answer重复
            'headimgurl' => '头像',
            'openid' => '微信Openid',
            'qq_open_id' => 'QQ_Open_ID',
            'aite_id' => 'Aite ID',
            'unionid' => '账号绑定ID',
            'wx_pc_openid' => '微信PC_Openid',
            'licence_image' => 'Licence Image',
            'brand_id_list' => '品牌ID',
            'servicer_user_id' => '绑定服务商ID',
            'servicer_super_id' => '省级服务商ID',
            'servicer_info_id' => '业务员信息ID',
            'bank_info_id' => '银行信息ID',
            'brand_admin_id' => '品牌商联系人',
            'nickname' => '姓名',
            'is_checked' => '审核状态',
            'checked_note' => '审核意见',
            'user_type' => '用户类别',
            'shopfront_pic' => '门头照片',
            'biz_license_pic' => '营业执照',
            'channel' => '渠道',
            'province' => '所在省份',
            'city' => '所在城市',
            'token_expired' => 'token失效时间',
            'int_balance' => '可用积分余额',
            'user_check_note' => '驳回原因',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //  商品最后操作人 即 操作绑定券的人
            $this->last_time = date('Y-m-d H:i:s', time());

            if (empty($this->servicer_user_id)) {
                $this->servicer_user_id = 0;
            }

            return true;
        } else {
            return false;
        }
        // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if(strlen($password) == 0) return;
        $this->password = $this->compile_password(['password'=>$password]);
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
                $this->ec_salt = ''.$ec_salt;
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
        $result = static::findOne(['access_token' => $token]);
        return $result;
    }

    /**
     * 获取用户 对应的等级折扣
     * @param $userId
     * @return float|int
     */
    public static function getUserRankDiscount($userId)
    {
        $userRankDiscount = 1;
        $rs = self::find()
            ->select(['user_rank'])
            ->where(['user_id' => $userId])
            ->one();

        if (!empty($rs)) {
            $discountMap = CacheHelper::getUserRankCache($rs->user_rank);
            if (!empty($discountMap)) {
                $userRankDiscount = $discountMap['discount'] / 100;
            }
        }

        return $userRankDiscount;
    }

    /**
     * 判定用户的默认地址是否有效，有效则返回默认地址
     * @param $userId
     * @return array|mixed
     */
    public static function checkDeafultAddress($userId)
    {
        $user = self::find()
            ->joinWith('defaultAddress')
            ->where([self::tableName().'.user_id' => $userId])
            ->one();

        if (!empty($user) && !empty($user->defaultAddress)) {
            if ($user->defaultAddress->check() == UserAddress::CHECK_VALID) {
                return $user->defaultAddress;
            }
        }

        return [];
    }

    /**
     * 刷新token，如果token未过期只刷新过期时间，如果token过期会生成新的token再刷新过期时间
     */
    public function updateAccessToken() {
        //多长时间不登录会过期
        $tokenDuration = 7 * 24 * 60 * 60;
        $now = date('Y-m-d H:i:s', strtotime('now'));
        //token已过期，重新生成token

        /*  暂时关闭 token过期时间的验证，先调通接口的使用，后面APP上线时要开启token时效验证
        if ($this->token_expired < date('Y-m-d H:i:s', strtotime('now') - $tokenDuration)) {
            $this->access_token = Yii::$app->security->generateRandomString();
            Yii::warning('token 过期 expired = '. $this->token_expired. ', now = '. $now. ', newToken = '. $this->access_token, __METHOD__);
        }
        */
        if (empty($this->access_token)) {
            $this->access_token = Yii::$app->security->generateRandomString();
        }

        $this->token_expired = $now;
        if ($this->validate() && $this->save()) {
            return true;
        }
        else {
            Yii::error('errors = '. VarDumper::export($this->errors), __METHOD__);
            return false;
        }
    }

    /**
     * 获取用户的默认收货地址
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(UserAddress::className(), ['address_id' => 'address_id']);
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    public function getServicerUserInfo() {
        return $this->hasOne(ServicerUserInfo::className(), ['id' => 'servicer_info_id']);
    }

    public function getBankinfo()
    {
        return $this->hasOne(BankInfo::className(), ['id' => 'bank_info_id']);
    }

    /**
     * 关联品牌商管理员表
     * @return \yii\db\ActiveQuery
     */
    public function getBrandAdmin()
    {
        return $this->hasOne(BrandAdmin::className(), ['id' => 'brand_admin_id']);
    }
    public function getDefaultAddress() {
        return $this->hasOne(UserAddress::className(), ['address_id' => 'address_id']);
    }

    public function getServicerUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'servicer_user_id']);
    }

    public function getSupserServicerUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'servicer_super_id']);
    }

    public function getTotalCash() {
        return CashRecord::totalCash();
    }

    /**
     * 关联积分流水表
     */
    public function getIntegrals()
    {
        return $this->hasMany(Integral::className(), ['user_id' => 'user_id']);
    }

    /**
     * 用户关联的区域
     * @return \yii\db\ActiveQuery
     */
    public function getUserRegion() {
        return $this->hasMany(UserRegion::className(), ['user_id' => 'user_id']);
    }

    /**
     * 获取用户区域
     * @return $this
     */
    public function getRegions() {
        return $this->hasMany(Region::className(), ['region_id' => 'region_id'])->viaTable(UserRegion::tableName(), [
            'user_id' => 'user_id',
        ]);
    }

    /**
     * 获取用户的省信息
     * @return \yii\db\ActiveQuery
     */
    public function getProvinceRegion() {
        return $this->hasOne(Region::className(), [
            'region_id' => 'province',
        ]);
    }

    /**
     * 获取用户的城市信息
     * @return \yii\db\ActiveQuery
     */
    public function getCityRegion() {
        return $this->hasOne(Region::className(), [
            'region_id' => 'city',
        ]);
    }

    public function getOrderGroups()
    {
        return $this->hasMany(OrderGroup::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo() {
        return $this->hasMany(OrderInfo::className(), [
            'user_id' => 'user_id',
        ]);
    }

    public function getLastOrder()
    {
        return $this->hasOne(OrderInfo::className(), [
            'user_id' => 'user_id',
        ])->orderBy([
            'order_id' => SORT_DESC,
        ]);
    }

    public function getSalemen() {
        return $this->hasMany(Users::className(), [
            'servicer_super_id' => 'user_id',
        ]);
    }

    public function getNotifyTimeModel() {
        return $this->hasOne(UsersNotifyTime::className(), [
            'user_id' => 'user_id',
        ]);
    }

    public function getNotifyTime() {
        if (empty($this->notifyTimeModel)) {
            return false;
        }
        return DateTimeHelper::getFormatCNDateTime($this->notifyTimeModel->notify_time);
    }

    public function updateNotifyTime() {
        if (!$this->getNotifyTime()) {
            $model = new UsersNotifyTime();
            $model->notify_time = DateTimeHelper::getFormatGMTDateTime();
            $this->link('notifyTimeModel', $model);
        }
        else {
            $this->notifyTimeModel->notify_time = DateTimeHelper::getFormatGMTDateTime();
            $this->notifyTimeModel->save();
        }
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            RecordCheckUserBehavior::className(),
        ]); // TODO: Change the autogenerated stub
    }

    public function getExtension()
    {
        return $this->hasOne(UserExtension::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return bool
     */
    public function getIdentify()
    {
        if(!empty($this->getExtension())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $note
     * @return bool
     * 通过审核
     */
    public function check()
    {
        $this->is_checked = self::IS_CHECKED_STATUS_PASSED;
        if($this->save()) {

            if($this->extension) {

                $this->extension->identify = UserExtension::HAS_IDENTIFY;
                $this->extension->save();

            }

            Yii::info($this->user_name.'通过审核');
            return true;
        } else {
            Yii::warning($this->user_name.'通过审核失败,原因是.'. VarDumper::export($this->errors));
            return false;
        }
    }

    /**
     * 拒绝审核
     */
    public function reject()
    {
        $this->is_checked = self::IS_CHECKED_STATUS_REFUSED;
        if($this->save()) {

            if($this->extension) {
                $this->extension->identify = UserExtension::REFUSE_IDENTIFY;
                $this->extension->save();
            }
            Yii::info($this->user_name.'拒绝通过审核');
            return true;
        } else {
            Yii::warning($this->user_name.'拒绝通过审核失败,原因是.'.VarDumper::export( $this->errors));
            return false;
        }
    }

    /**
     * @param string $note
     * @param string $userCheckNote
     * @return bool
     * 拉黑用户
     */
    public function black()
    {
        $this->is_checked = self::IS_CHECKED_STATUS_BLACK;
        if($this->save()) {
            if($this->extension) {
                $this->extension->identify = UserExtension::REFUSE_IDENTIFY;
                $this->extension->save();
            }
            Yii::info($this->user_name.'拉黑成功');
            return true;
        } else {
            Yii::warning($this->user_name.'拉黑失败,原因是.'. VarDumper::export($this->errors));
            return false;
        }
    }

    /**
     * 未审核
     */
    public function unCheck()
    {
        $this->is_checked = self::IS_CHECKED_STATUS_IN_REVIEW;
        if($this->save()) {
            if($this->extension) {
                $this->extension->identify = UserExtension::NOT_IDENTIFY;
                $this->extension->save();
            }
            Yii::info($this->user_name.'修改为待认证成功');
            return true;
        } else {
            Yii::warning($this->user_name.'修改为待认证失败,原因是.'. VarDumper::export($this->errors));
            return false;
        }
    }
    public static function getServicerUserMap() {
        $servicer_list = CacheHelper::getServicerCache();
        $servicer_map = [];
        foreach ($servicer_list as $servicer) {
            if ($servicer['nickname']) {
                $servicer_map[$servicer['user_id']] = $servicer['nickname'].' | '.$servicer['user_name']. ' | '. $servicer['mobile_phone'];
            } else {
                $servicer_map[$servicer['user_id']] = $servicer['user_name']. ' | '. $servicer['mobile_phone'];
            }
        }
        return $servicer_map;
    }

    public function getShowName() {
        return $this->nickname. '('. $this->user_name. ')';
    }

    public function getUserShowName() {
        return empty($this->nickname) ? $this->user_name : $this->nickname;
    }

    /**
     * 获取用户的默认地址信息
     *
     * @param int $userId
     * @return array
     */
    public static function getUserDefaultAddress($userId = 0)
    {
        //  获取用户默认地址 的 省份
        $defaultProvinceId = 2;   //  如果用户没有设置默认地址，则使用北京市作为默认地址
        $defaultProvinceName = '北京市';   //  如果用户没有设置默认地址，则使用北京市作为默认地址
        if (!empty($userId)) {
            $userModel = self::find()
                ->joinWith([
                    'defaultAddress',
                    'defaultAddress.provinceName',
                ])->where([
                    self::tableName().'.user_id' => $userId
                ])->one();

            $defaultProvinceId = $userModel->defaultAddress->province;
            if (!empty($userModel) && !empty($userModel->defaultAddress->provinceName->region_name)) {
                $defaultProvinceName = $userModel->defaultAddress->provinceName->region_name;
            }
        }

        return [
            'provinceId' => $defaultProvinceId,
            'cityId' => $userModel->defaultAddress->city,
            'districtId' => $userModel->defaultAddress->district,

            'provinceName' => $defaultProvinceName,
        ];
    }

    /**
     * 设置默认收货地址
     * 地址是否属于该用户在上层逻辑中判断
     *
     * @param $user_id
     * @param $address_id
     * @return bool
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public static function setDefaultAddress($user_id, $address_id)
    {
        $model = self::findOne($user_id);
        if ($model) {
            $model->setAttribute('address_id', $address_id);

            if ($model->save()) {
                return true;
            } else {
                throw new ServerErrorHttpException('设置默认地址失败', 2);
            }
        } else {
            throw new BadRequestHttpException('用户信息不存在', 1);
        }
    }
}
