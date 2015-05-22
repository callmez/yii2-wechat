<?php

namespace callmez\wechat\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use callmez\wechat\behaviors\EventBehavior;
use callmez\wechat\components\Wechat as WechatSDK;

/**
 * This is the model class for table "{{%wechat}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $hash
 * @property string $token
 * @property string $access_token
 * @property string $account
 * @property string $original
 * @property integer $type
 * @property string $app_id
 * @property string $app_secret
 * @property integer $encoding_type
 * @property string $encoding_aes_key
 * @property string $avatar
 * @property string $qrcode
 * @property string $address
 * @property string $description
 * @property string $username
 * @property integer $status
 * @property string $password
 * @property integer $created_at
 * @property integer $updated_at
 */
class Wechat extends ActiveRecord
{
    /**
     * 未激活状态
     */
    const STATUS_INACTIVE = 0;
    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 1;
    /**
     * 删除状态
     */
    const STATUS_DELETED = -1;
    /**
     * 普通订阅号
     */
    const TYPE_SUBSCRIBE = 0;
    /**
     * 认证订阅号
     */
    const TYPE_SUBSCRIBE_VERIFY = 1;
    /**
     * 普通服务号
     */
    const TYPE_SERVICE = 2;
    /**
     * 认证服务号
     */
    const TYPE_SERVICE_VERIFY = 3;
    /**
     * 认证企业号
     */
    const TYPE_ENTERPRISE = 4;
    /**
     * 消息加密模式 普通模式
     */
    const ENCODING_NORMAL = 0;
    /**
     * 消息加密模式 兼容模式
     */
    const ENCODING_COMPATIBLE = 1;
    /**
     * 消息加密模式 安全模式
     */
    const ENCODING_SAFE = 2;
    /**
     * 公众号类型列表
     * @var array
     */
    public static $types = [
        self::TYPE_SUBSCRIBE => '订阅号',
        self::TYPE_SUBSCRIBE_VERIFY => '认证订阅号',
        self::TYPE_SERVICE_VERIFY => '认证服务号',
//        self::TYPE_ENTERPRISE => '认证企业号',
    ];
    public static $statuses = [
        self::STATUS_INACTIVE => '未接入',
        self::STATUS_ACTIVE => '已接入',
        self::STATUS_DELETED => '已删除'
    ];
    /**
     * 消息加密模式列表
     * @var array
     */
    public static $encodings = [
        self::ENCODING_NORMAL => '普通模式',
        self::ENCODING_COMPATIBLE => '兼容模式',
        self::ENCODING_SAFE => '安全模式'
    ];

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className(),
            'event' => [
                'class' => EventBehavior::className(),
                'events' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => function ($event) {
                        $this->hash = $this->generateUniqueHashValue(); // 生成唯一Hash
                        $this->access_token = serialize($this->access_token);
                    },
                    ActiveRecord::EVENT_BEFORE_UPDATE => function ($event) {
                        $this->access_token = serialize($this->access_token);
                    },
                    ActiveRecord::EVENT_AFTER_FIND => function ($event) {
                        $this->access_token = unserialize($this->access_token);
                    }
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'account', 'original', 'type', 'token', 'encoding_type', 'app_id', 'app_secret', 'avatar', 'qrcode'], 'required', 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['type', 'encoding_type', 'status'], 'integer', 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['name', 'original', 'username'], 'string', 'max' => 40, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['token', 'password'], 'string', 'max' => 32, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['address', 'description', 'avatar', 'qrcode'], 'string', 'max' => 255, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['account'], 'string', 'max' => 30, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['app_id', 'app_secret'], 'string', 'max' => 50, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['encoding_aes_key'], 'string', 'max' => 43, 'except' => ['avatarUpload', 'qrcodeUpload']],

            [['avatar'], 'file', 'extensions' => 'gif, jpg', 'on' => 'avatarUpload'],
            [['qrcode'], 'file', 'extensions' => 'gif, jpg', 'on' => 'qrcodeUpload']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '公众号ID',
            'name' => '公众号名称',
            'hash' => '公众号名称',
            'token' => '微信服务Token(令牌)',
            'access_token' => '微信服务访问Token',
            'account' => '微信号',
            'original' => '原始ID',
            'type' => '公众号类型',
            'app_id' => 'AppID(应用ID)',
            'app_secret' => 'AppSecret(应用密钥)',
            'encoding_type' => '消息加密方式',
            'encoding_aes_key' => '消息加密秘钥EncodingAesKey',
            'avatar' => '头像地址',
            'qrcode' => '二维码地址',
            'address' => '所在地址',
            'description' => '公众号简介',
            'username' => '微信官网登录名(邮箱)',
            'status' => '状态',
            'password' => '微信官网登录密码',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @var WechatSDK
     */
    private $_sdk;

    /**
     * 获取实例化后的公众号SDK类
     * @return mixed|object
     */
    public function getSdk()
    {
        if ($this->_sdk === null) {
            $this->_sdk = Yii::createObject([
                'class' => WechatSDK::className(),
                'model' => $this
            ]);
        }
        return $this->_sdk;
    }

    /**
     * 生成唯一的hash值
     * @return string
     */
    protected function generateUniqueHashValue()
    {
        $hash = Yii::$app->security->generateRandomString(5);
        if (static::find()->where(['hash' => $hash])->exists()) { // 生成最终唯一的hash
            $hash = $this->generateUniqueHashValue();
        }
        return $hash;
    }
}
