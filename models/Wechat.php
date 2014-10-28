<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 微信公众号
 * @package callmez\wechat\models
 */
class Wechat extends ActiveRecord
{
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
        self::TYPE_SUBSCRIBE_VERIFY => '认证服务号',
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
            [['name', 'hash', 'type', 'token',  'account', 'orginal'], 'required'],
            [['app_id', 'app_secret'], 'required', 'when' => function($model) {
                return $model->type != Wechat::TYPE_SUBSCRIBE;
            }, 'whenClient' => 'function (attribute, value) {
                return $("[name=\'' . $this->formName() . '[type]\']:checked").val() == "' . Wechat::TYPE_SUBSCRIBE . '";
            }']
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
            'orginal' => '原始号',
            'account' => '微信号',
            'type' => '公众号接口类型',
            'token' => '微信Token',
            'app_id' => '公众号AppId',
            'app_secret' => '公众号AppSecret',
            'encoding_type' => '公众号消息加密方式',
            'encoding_aes_key' => '公众号消息加密秘钥',
        ];
    }

    public function beforeSave($insert)
    {
        $return = parent::beforeSave($insert);
        if ($return) {
            $this->access_token = serialize($this->access_token);
        }
        return $return;
    }

    public function afterFind()
    {
        $this->access_token = unserialize($this->access_token);
        return parent::afterFind();
    }

}