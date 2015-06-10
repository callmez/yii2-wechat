<?php

namespace callmez\wechat\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use callmez\wechat\behaviors\EventBehavior;
use callmez\wechat\components\MpWechat;

/**
 * 公众号数据
 * @package callmez\wechat\models
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
     * 公众号类型列表
     * @var array
     */
    public static $types = [
        self::TYPE_SUBSCRIBE => '订阅号',
        self::TYPE_SUBSCRIBE_VERIFY => '认证订阅号',
        self::TYPE_SERVICE_VERIFY => '认证服务号',
    ];
    public static $statuses = [
        self::STATUS_INACTIVE => '未接入',
        self::STATUS_ACTIVE => '已接入',
        self::STATUS_DELETED => '已删除'
    ];

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className(),
            'event' => [
                'class' => EventBehavior::className(),
                'events' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => function ($event) {
                        $this->access_token && $this->access_token = serialize($this->access_token);
                    },
                    ActiveRecord::EVENT_BEFORE_UPDATE => function ($event) {
                        $this->access_token && $this->access_token = serialize($this->access_token);
                    },
                    ActiveRecord::EVENT_AFTER_FIND => function ($event) {
                        $this->access_token && $this->access_token = unserialize($this->access_token);
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
            [['name', 'account', 'original', 'type', 'token', 'key', 'secret', 'encoding_aes_key', 'avatar', 'qrcode'], 'required', 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['type', 'status'], 'integer', 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['name', 'original', 'username'], 'string', 'max' => 40, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['token', 'password'], 'string', 'max' => 32, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['address', 'description', 'avatar', 'qrcode'], 'string', 'max' => 255, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['account'], 'string', 'max' => 30, 'except' => ['avatarUpload', 'qrcodeUpload']],
            [['key', 'secret'], 'string', 'max' => 50, 'except' => ['avatarUpload', 'qrcodeUpload']],
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
            'token' => '微信服务Token(令牌)',
            'access_token' => 'AccessToken(访问令牌)',
            'account' => '微信号',
            'original' => '原始ID',
            'type' => '公众号类型',
            'key' => 'AppID(应用ID)',
            'secret' => 'AppSecret(应用密钥)',
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

            'apiUrl' => 'API地址'
        ];
    }

    public function attributeHints()
    {
        return [
            'apiUrl' => '请复制该内容填写到微信后台->开发者中心->服务器配置并确定Token和EncodingAesKey和微信后台的设置保持一致.'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return Yii::createObject(WechatQuery::className(), [get_called_class()]);
    }

    /**
     * 返回公众号微信接口链接
     * @param boolean|string $scheme the URI scheme to use in the generated URL:
     * @return string
     */
    public function getApiUrl($scheme = true)
    {
        return Url::toRoute([
            '/wechat/' . Yii::$app->getModule('wechat')->apiRoute,
            'id' => $this->id
        ], $scheme);
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
            $this->_sdk = Yii::createObject(MpWechat::className(), [$this]);
        }
        return $this->_sdk;
    }
}
