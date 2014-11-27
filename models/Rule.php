<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 规则表
 * @package callmez\wechat\models
 */
class Rule extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_ACTIVE = 0;

    public static function tableName()
    {
        return '{{%wechat_rule}}';
    }

    public function rules()
    {
        return [
            [['wid', 'name'], 'required'],
            [['priority'], 'number', 'min' => 0, 'max' => 255],
            [['wid', 'status', 'priority'], 'integer', 'integerOnly' => true]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '规则名称',
            'priority' => '优先级',
            'status' => '状态',
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