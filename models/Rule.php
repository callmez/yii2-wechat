<?php
namespace callmez\wechat\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;

/**
 * 微信服务规则表
 * @package callmez\wechat\models
 */
class Rule extends ActiveRecord
{
    const TYPE_REPLY = 0;
    const TYPE_PROCESSOR= 1;
    public static $types = [
        self::TYPE_REPLY => '自动回复',
        self::TYPE_PROCESSOR => '接口回复'
    ];
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;
    const STATUS_DELETED = -1;
    public static $statuses = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用',
        self::STATUS_DELETED => '删除'
    ];

    public static function tableName()
    {
        return '{{%wechat_rule}}';
    }

    public static function find()
    {
        return Yii::createObject(RuleQuery::className(), [get_called_class()])
            ->andWhere([self::tableName() . '.status' => self::STATUS_ACTIVE]);
    }

    public function rules()
    {
        return [
            [['wid', 'name', 'type'], 'required'],
            [['wid', 'status', 'priority'], 'integer'],
            [['reply'], 'required', 'when' => function ($model) {
                    return $model->type == self::TYPE_REPLY;
                }, 'whenClient' => 'function (attribute, value) {
                return $("[name=\'' . Html::getInputName($this, 'type') . '\']:checked").val() == "' . self::TYPE_REPLY . '";
            }'],
            [['processor'], 'required', 'when' => function ($model) {
                    return $model->type == self::TYPE_PROCESSOR;
                }, 'whenClient' => 'function (attribute, value) {
                return $("[name=\'' . Html::getInputName($this, 'type') . '\']:checked").val() == "' . self::TYPE_PROCESSOR . '";
            }'],
            [['priority'], 'number', 'min' => 0, 'max' => 255],
            [['priority'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'wid' => '微信公众号ID',
            'name' => '规则名称',
            'priority' => '优先级',
            'status' => '状态',
            'type' => '回复类型',
            'processor' => '处理接口',
            'reply' => '自动回复内容',
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

    public function getKeywords()
    {
        return $this->hasMany(RuleKeyword::className(), ['rid' => 'id']);
    }
}