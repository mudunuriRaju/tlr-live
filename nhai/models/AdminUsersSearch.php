<?php

namespace nhai\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AdminUsers;

/**
 * AdminUsersSearch represents the model behind the search form about `backend\models\AdminUsers`.
 */
class AdminUsersSearch extends AdminUsers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_user_id', 'language_id', 'type', 'status'], 'integer'],
            [['email', 'firstname', 'lastname', 'password', 'password_hash', 'phone', 'location', 'created_on', 'updated_on'], 'safe'],
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
        $query = AdminUsers::find();

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
            'admin_user_id' => $this->admin_user_id,
            'language_id' => $this->language_id,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }
}
