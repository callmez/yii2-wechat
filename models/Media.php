<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 素材存储表
 * @package callmez\wechat\models
 */
class Media extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_wechat_media}}';
    }
}