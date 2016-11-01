<?php

namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Users;

/**
 * models represents the model behind the search form about `backend\models\Users`.
 */
class models extends Users
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_type_id', 'status', 'language_id'], 'integer'],
            [['firstname', 'lastname', 'user_email', 'password', 'access_token', 'created_on', 'expiry_date'], 'safe'],
            [['amount'], 'number'],
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
        $query = Users::find();

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
            'user_id' => $this->user_id,
            'user_type_id' => $this->user_type_id,
            'status' => $this->status,
            'created_on' => $this->created_on,
            'expiry_date' => $this->expiry_date,
            'language_id' => $this->language_id,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'user_email', $this->user_email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'access_token', $this->access_token]);

        return $dataProvider;
    }

}
