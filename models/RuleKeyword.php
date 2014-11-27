<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

/**
 * 规则出发关键字
 * @package callmez\wechat\models
 */
class RuleKeyword extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_ACTIVE = 0;
    const TYPE_MATCH = 0;
    const TYPE_INCLUDE = 1;
    const TYPE_REGULAR = 2;
    public static $types = [
        self::TYPE_MATCH => '直接匹配关键字',
        self::TYPE_INCLUDE => '包含关键字',
        self::TYPE_REGULAR => '正则匹配关键字'
    ];
    public static function tableName()
    {
        return '{{%wechat_rule_keyword}}';
    }

    public function rules()
    {
        return [
            [['rid', 'keyword', 'type', ], 'required'],
            [['priority'], 'number', 'min' => 0, 'max' => 255],
            [['rid', 'type', 'status', 'priority'], 'integer', 'integerOnly' => true]
        ];
    }

    public function attributeLabels()
    {
        return [
            'keyword' => '关键字',
            'type' => '匹配类型',
            'priority' => '优先级'
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

    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['wid' => 'id']);
    }

    /**
     * 查询匹配的关键字
     * @param $keyword
     * @param int $wid 公众号id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllByKeyword($keyword, $wid = null)
    {
        $query = RuleKeyword::find();
        if ($wid !== null) {
            $query->joinWith([
                'rule' => function($query) use ($wid) {
                    $query->andWhere(['wid' => $wid]);
                }
            ]);
        }
        $conditons = [
            'or',
            ['and', 'type=:typeMatch', 'keyword=:keyword'], // 直接匹配关键字
            ['and', 'type=:typeInclude', 'INSTR(:keyword, keyword) > 0'], // 包含关键字
            ['and', 'type=:typeRegular', ':keyword REGEXP keyword'] // 正则匹配关键字
        ];
        $params = [
            ':keyword' => $keyword,
            ':typeMatch' => RuleKeyword::TYPE_MATCH,
            ':typeInclude' => RuleKeyword::TYPE_INCLUDE,
            ':typeRegular' => RuleKeyword::TYPE_REGULAR
        ];
        return $query->where($conditons)->addParams($params)->all();
    }
}