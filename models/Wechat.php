<?php
namespace callmez\wechat\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\validators\UniqueValidator;

/**
 * 微信公众号表
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
    public static $encodings = [
        self::ENCODING_NORMAL => '普通模式',
        self::ENCODING_COMPATIBLE => '兼容模式',
        self::ENCODING_SAFE => '安全模式'
    ];

    /**
     * 菜单类型
     * 注: 有value属性的在提交菜单是该类型的值必须设置为此值, 没有的则不限制
     * @var array
     */
    public static $menuTypes = [
        'click' => [
            'name' => '关键字',
            'meta' => 'key',
            'alert' => '用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；'
        ],
        'view' => [
            'name' => '链接',
            'meta' => 'url',
            'alert' => '用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。'
        ],
        'scancode_waitmsg' => [
            'name' => '启动扫码并接收服务器消息',
            'meta' => 'key',
            'value' => 'rselfmenu_0_0',
            'alert' => '用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。'
        ],
        'scancode_push' => [
            'name' => '启动扫码',
            'meta' => 'key',
            'value' => 'rselfmenu_0_1',
            'alert' => '用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。'
        ],
        'pic_sysphoto' => [
            'name' => '启动拍照',
            'meta' => 'key',
            'value' => 'rselfmenu_1_0',
            'alert' => '用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。'
        ],
        'pic_photo_or_album' => [
            'name' => '启动拍照或者相册',
            'meta' => 'key',
            'value' => 'rselfmenu_1_1',
            'alert' => '用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。'
        ],
        'pic_weixin' => [
            'name' => '启动微信相册',
            'meta' => 'key',
            'value' => 'rselfmenu_1_2',
            'alert' => '用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。'
        ],
        'location_select' => [
            'name' => '发送位置信息',
            'meta' => 'key',
            'value' => 'rselfmenu_2_0',
            'alert' => '用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。'
        ]
    ];

    public static function tableName()
    {
        return '{{%wechat}}';
    }

    public static function find()
    {
        return parent::find()->andWhere([self::tableName() . '.status' => self::STATUS_ACTIVE]);
    }

    public function rules()
    {
        return [
            [['name', 'app_id', 'app_secret', 'type', 'token', 'account', 'original', 'encoding_type'], 'required'],
            [['encoding_aes_key'], 'required', 'when' => function ($model) { // 安全模式下encoding_aes_key必填
                return $model->encoding_type == self::ENCODING_SAFE;
            }, 'whenClient' => 'function (attribute, value) {
                return $("[name=\'' . Html::getInputName($this, 'encoding_type') . '\']:checked").val() == "' . self::ENCODING_SAFE . '";
            }'],
            [['original'], 'unique'],
            [['type', 'encoding_type'], 'integer'],
            [['access_token'], 'default', 'value' => ''],
            [['encoding_aes_key', 'address', 'description', 'avatar', 'qr_code'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
            'serialize' => [
                'class' => 'yii\behaviors\AttributeBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'access_token',
                ],
                'value' => function ($event) {
                    if (is_array($event->sender->access_token)) {
                        $event->sender->access_token = serialize($event->sender->access_token);
                    }
                    return $event->sender->access_token;
                }
            ],
            'unserialize' => [
                'class' => 'yii\behaviors\AttributeBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'access_token',
                    ActiveRecord::EVENT_AFTER_INSERT => 'access_token',
                    ActiveRecord::EVENT_AFTER_UPDATE => 'access_token',
                ],
                'value' => function ($event) {
                    if (is_string($event->sender->access_token)) {
                        $event->sender->access_token = unserialize($event->sender->access_token);
                    }
                    return $event->sender->access_token;
                }
            ]
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
        if ($insert) {
            $this->hash = $this->generateUniqueHashValue();
        }
        return parent::beforeSave($insert);
    }

    /**
     * 生成唯一的hash值
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