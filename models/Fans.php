<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 微信粉丝表
 * @package callmez\wechat\models
 */
class Fans extends ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%wechat_fans}}';
    }
}