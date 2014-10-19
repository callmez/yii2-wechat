<?php
namespace yii\wechat\models;

use yii\db\ActiveRecord;

/**
 * 规则表
 * @package yii\wechat\models
 */
class Rule extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_UNVERIFY = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%rule}}';
    }

    public function rules()
    {
        return [
            [['wid', 'name'], 'required'],
            [['wid', 'status', 'order'], 'integer', 'integerOnly' => true]
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
}