<?php

namespace callmez\wechat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use callmez\wechat\models\Fans;

/**
 * FansSearch represents the model behind the search form about `callmez\wechat\models\Fans`.
 */
class FansSearch extends Fans
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wid', 'status'], 'integer'],
            [['open_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Fans::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'wid' => $this->wid,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id]);

        return $dataProvider;
    }
}
