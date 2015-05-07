<?php

namespace callmez\wechat\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%wechat_reply_rule}}".
 *
 * @property integer $id
 * @property integer $wid
 * @property string $name
 * @property string $module
 * @property integer $status
 * @property integer $priority
 * @property integer $created_at
 * @property integer $updated_at
 */
class ReplyRule extends \yii\db\ActiveRecord
{
    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 1;
    /**
     * 禁用状态
     */
    const STATUS_DISABLED = 0;
    public static $statuses = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用'
    ];

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_reply_rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'name', 'module', 'status'], 'required'],
            [['wid', 'status', 'priority'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['module'], 'string', 'max' => 20],
            [['priority'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wid' => '所属微信公众号ID',
            'name' => '规则名称',
            'module' => '处理模块',
            'status' => '状态',
            'priority' => '优先级',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function getReplyKeywords()
    {
        return $this->hasMany(ReplyRuleKeyword::className(), ['rid' => 'id'])
            ->inverseOf('rule');
    }
}
