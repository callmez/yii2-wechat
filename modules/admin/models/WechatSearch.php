<?php

namespace callmez\wechat\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use callmez\wechat\models\Wechat;

class WechatSearch extends Wechat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'encoding_type', 'status'], 'integer'],
            [['name', 'hash', 'token', 'access_token', 'account', 'original', 'app_id', 'app_secret', 'encoding_aes_key', 'avatar', 'qr_code', 'address', 'description', 'username', 'password'], 'safe'],
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
        $query = Wechat::find();

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
            'type' => $this->type,
            'encoding_type' => $this->encoding_type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'hash', $this->hash])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'original', $this->original])
            ->andFilterWhere(['like', 'app_id', $this->app_id])
            ->andFilterWhere(['like', 'app_secret', $this->app_secret])
            ->andFilterWhere(['like', 'encoding_aes_key', $this->encoding_aes_key])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'qr_code', $this->qr_code])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password]);

        return $dataProvider;
    }
}
