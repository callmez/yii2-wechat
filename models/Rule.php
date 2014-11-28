<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 规则表
 * @package callmez\wechat\models
 */
class Rule extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;
    const STATUS_DELETED = -1;
    public static $statuses = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用',
        self::STATUS_DISABLED => '删除'
    ];

    public static function tableName()
    {
        return '{{%wechat_rule}}';
    }

    public function rules()
    {
        return [
            [['wid', 'name'], 'required'],
            [['priority'], 'number', 'min' => 0, 'max' => 255],
            [['priority'], 'default', 'value' => 0],
            [['wid', 'status', 'priority'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'wid' => '微信公众号ID',
            'name' => '规则名称',
            'priority' => '优先级',
            'status' => '状态',
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
        ];
    }

    public static function find()
    {
        return parent::find()->andWhere(['status' => self::STATUS_ACTIVE]);
    }

    public function getKeywords()
    {
        return $this->hasMany(RuleKeyword::className(), ['rid' => 'id']);
    }
}