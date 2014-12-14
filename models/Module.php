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
    public static function tableName()
    {
        return  '{{%wechat_module}}';
    }
}