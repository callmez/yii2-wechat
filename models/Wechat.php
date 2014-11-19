<?php
namespace callmez\wechat\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\validators\UniqueValidator;

/**
 * 微信公众号
 * @package callmez\wechat\models
 */
class Wechat extends ActiveRecord
{
    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 0;
    /**
     * 删除状态
     */
    const STATUS_DELETE = -1;
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
        self::TYPE_SUBSCRIBE => '普通订阅号',
        self::TYPE_SUBSCRIBE_VERIFY => '认证订阅号',
        self::TYPE_SERVICE_VERIFY => '认证服务号',
        self::TYPE_ENTERPRISE => '认证企业号',
    ];

    /**
     * 消息加密模式列表
     * @var array
     */
    public static $encodingTypes = [
        self::ENCODING_NORMAL => '普通模式',
        self::ENCODING_COMPATIBLE => '兼容模式',
        self::ENCODING_SAFE => '安全模式'
    ];

    public static function tableName()
    {
        return '{{%wechat}}';
    }

    public function rules()
    {
        return [
            [['name', 'app_id', 'app_secret', 'type', 'token',  'account', 'original', 'encoding_type'], 'required'],
            [['original'], 'unique'],
            [['type', 'encoding_type'], 'integer'],
            [['encoding_aes_key', 'address', 'description', 'avatar', 'qr_code'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '公众号名称',
            'original' => '原始号',
            'account' => '微信号',
            'type' => '公众号接口类型',
            'token' => '微信Token',
            'app_id' => '公众号AppId',
            'app_secret' => '公众号AppSecret',
            'encoding_type' => '公众号消息加密方式',
            'encoding_aes_key' => '公众号消息加密秘钥',
            'address' => '地址',
            'description' => '公众号描述',
            'avatar' => '头像',
            'qr_code' => '二维码',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
        ];
    }

    public function beforeSave($insert)
    {
        $this->access_token = serialize($this->access_token);
        if ($insert) {
            $this->generateUniqueHashValue();
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->access_token = unserialize($this->access_token) ?: [];
        return parent::afterFind();
    }

    /**
     * 生成唯一的hash值
     */
    protected function generateUniqueHashValue()
    {
        $this->hash = Yii::$app->security->generateRandomString(5);
        if (static::find()->where(['hash' => $this->hash])->exists()) {
            $this->generateHashValue();
        }
    }
}