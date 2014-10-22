<?php
namespace yii\wechat\models;

use yii\db\ActiveRecord;

/**
 * 规则出发关键字
 * @package yii\wechat\models
 */
class RuleKeyword extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_UNVERIFY = 0;
    const STATUS_ACTIVE = 1;
    const TYPE_MATCH = 0;
    const TYPE_INCLUDE = 1;
    const TYPE_REGULAR = 2;
    public static function tableName()
    {
        return '{{%rule_keyword}}';
    }

    public function rules()
    {
        return [
            [['rid', 'keyword', 'type', ], 'required'],
            [['rid', 'type', 'status', 'order'], 'integer', 'integerOnly' => true]
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

    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['wid' => 'id']);
    }


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
            ['and', 'type=:typeMatch', 'keyword=:keyword'],
            ['and', 'type=:typeInclude', 'INSTR(:keyword, keyword) > 0'],
            ['and', 'type=:typeRegular', ':keyword REGEXP keyword']
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