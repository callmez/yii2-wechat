<?php

namespace callmez\wechat\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%wechat_rule_keyword}}".
 *
 * @property integer $id
 * @property integer $rid
 * @property string $keyword
 * @property string $type
 * @property integer $priority
 * @property integer $created_at
 * @property integer $updated_at
 */
class RuleKeyword extends \yii\db\ActiveRecord
{
    const TYPE_MATCH = 0;
    const TYPE_INCLUDE = 1;
    const TYPE_REGULAR = 2;
    public static $types = [
        self::TYPE_MATCH => '直接匹配关键字',
        self::TYPE_INCLUDE => '包含关键字',
        self::TYPE_REGULAR => '正则匹配关键字'
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
        return '{{%wechat_rule_keyword}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rid', 'keyword', 'type'], 'required'],
            [['rid', 'priority', 'type', 'created_at', 'updated_at'], 'integer'],
            [['keyword'], 'string', 'max' => 255],
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
            'rid' => '所属规则ID',
            'keyword' => '规则关键字',
            'type' => '关键字类型',
            'priority' => '优先级',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rid']);
    }

    /**
     * 根据关键字查找匹配的规则
     * @param $keyword
     * @param null $wid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllByKeyword($keyword, $wid = null)
    {
        $query = RuleKeyword::find();
        $query->andWhere($conditons = [
                'or',
                ['and', '{{type}}=:typeMatch', '{{keyword}}=:keyword'], // 直接匹配关键字
                ['and', '{{type}}=:typeInclude', 'INSTR(:keyword, {{keyword}})>0'], // 包含关键字
                ['and', '{{type}}=:typeRegular', ':keyword REGEXP {{keyword}}'] // 正则匹配关键字
            ])
            ->addParams([
                ':keyword' => $keyword,
                ':typeMatch' => RuleKeyword::TYPE_MATCH,
                ':typeInclude' => RuleKeyword::TYPE_INCLUDE,
                ':typeRegular' => RuleKeyword::TYPE_REGULAR
            ]);
        if ($wid !== null) {
            $query->joinWith([
                'rule' => function($query) use ($wid) {
                    $query->andWhere(['wid' => $wid]);
                }
            ]);
        }
        return $query->all();
    }
}
