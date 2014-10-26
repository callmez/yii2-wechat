<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 公众号关注者
 * @package callmez\wechat\models
 */
class Fans extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%wechat_fans}}';
    }
}