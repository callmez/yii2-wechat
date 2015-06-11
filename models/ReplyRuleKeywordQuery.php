<?php
namespace callmez\wechat\models;

use yii\db\ActiveQuery;

class ReplyRuleKeywordQuery extends ActiveQuery
{
    /**
     * 文本类型关键字过滤
     * @param $keyword
     * @return $this
     */
    public function keyword($keyword)
    {
        $this->andWhere([
            'or',
            ['and', '{{type}}=:typeMatch', '{{keyword}}=:keyword'], // 直接匹配关键字
            ['and', '{{type}}=:typeInclude', 'INSTR(:keyword, {{keyword}})>0'], // 包含关键字
            ['and', '{{type}}=:typeRegular', ':keyword REGEXP {{keyword}}'], // 正则匹配关键字
        ])
        ->addParams([
            ':keyword' => $keyword,
            ':typeMatch' => ReplyRuleKeyword::TYPE_MATCH,
            ':typeInclude' => ReplyRuleKeyword::TYPE_INCLUDE,
            ':typeRegular' => ReplyRuleKeyword::TYPE_REGULAR
        ]);
        return $this;
    }

    /**
     * 查询公众号规则
     * @return $this
     */
    public function wechatRule($wid, $status = ReplyRule::STATUS_ACTIVE)
    {
        $this->joinWith([
            'rule' => function($query) use ($wid, $status) {
                if ($status !== null) {
                    $query->active($status);
                }
                $query->andWhere(['wid' => $wid]);
            }
        ]);
        return $this;
    }

    /**
     * 过滤有效时间
     * @param $time
     * @return $this
     */
    public function limitTime($time)
    {
        $this->andWhere([
            'and',
            ['or', 'start_at<:time', 'start_at=0'],
            ['or', 'end_at>:time', 'end_at=0']
        ])
        ->addParams([
            ':time' => $time
        ]);
        return $this;
    }
}