<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 微信扩展模块表
 * @package callmez\wechat\models
 */
class Module extends ActiveRecord
{
    const TYPE_BUSSINESS = 'bussiness';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_ACTIVITY = 'activity';
    const TYPE_SERVICES = 'services';
    const TYPE_OTHER = 'other';
    const SERVICE_MOBILE = 'mobile';
    const SERVICE_PROCESSOR = 'processor';
    const SERVICE_RECEIVER = 'receiver';
    /**
     * 业务类型
     * @var array
     */
    public static $types = [
        'bussiness' => '主营业务',
        'customer' => '客户关系',
        'activity' => '营销活动',
        'services' => '基础服务',
        'other' => '其他类别'
    ];
    /**
     * 组件服务
     * @var array
     */
    public static $serviceTypes = [
        'mobile' => '移动页面服务',
        'processor' => '微信消息服务',
        'receiver' => '微信订阅服务'
    ];
    /**
     * 默认数据
     * @var array
     */
    public static $default = [
        'name' => null,
        'module' => null,
        'description' => null,
        'version' => '1.0',
        'type' => 'other',
        'author' => null,
        'link' => null,
        'services' => []
    ];

    public static function tableName()
    {
        return  '{{%wechat_module}}';
    }

    public function rules()
    {
        return [
            [['name', 'module', 'class', 'version', 'type', 'author', 'services'], 'required'],
            [['module'], 'unique'],
            [['description'], 'default', 'value' => ''],
            [['link'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '模块名称',
            'module' => '模块标识',
            'version' => '版本号',
            'author' => '作者',
            'description' => '详细描述',
            'link' => '详细链接',
            'services' => '服务组件',
            'created_at' => '创建时间',
            'updated_at' => '更新时间'
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'services',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'services',
                ],
                'value' => function ($event) {
                    if (is_array($event->sender->services)) {
                        $event->sender->services = serialize($event->sender->services);
                    }
                    return $event->sender->services;
                }
            ],
            'unserialize' => [
                'class' => 'yii\behaviors\AttributeBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'services',
                    ActiveRecord::EVENT_AFTER_INSERT => 'services',
                    ActiveRecord::EVENT_AFTER_UPDATE => 'services',
                ],
                'value' => function ($event) {
                    if (is_string($event->sender->services)) {
                        $event->sender->services = unserialize($event->sender->services);
                    }
                    return $event->sender->services;
                }
            ]
        ];
    }
}