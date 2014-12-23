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
    public static $types = [
        'bussiness' => '主营业务',
        'customer' => '客户关系',
        'activity' => '营销活动',
        'services' => '基础服务',
        'other' => '其他类别'
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
        'link' => null
    ];

    public static function tableName()
    {
        return  '{{%wechat_module}}';
    }

    public function rules()
    {
        return [
            [['name', 'module'], 'required'],
            [['module'], 'unique'],
            [['description'], 'default', 'value' => '']
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
        ];
    }


}