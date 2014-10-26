<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

class Module extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%wechat_module}}';
    }
}