<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Tolls;
use backend\models\TollUsers;

/**
 * Laguage represents the model behind the search form about `backend\models\MasterLanguage`.
 */
class TollSearch extends Tolls
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['toll_unique_number', 'toll_location', 'toll_lat', 'toll_lng', 'toll_radius', 'amount', 'allowed_ip'], 'required'],
            [['toll_unique_number', 'toll_location'], 'safe'],
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
        $query = Tolls::find();
        $query->joinWith('tollUsers');
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
            'toll_unique_number' => $this->toll_unique_number,
        ]);

        $query->andFilterWhere(['like', 'toll_unique_number', $this->toll_unique_number])
            ->andFilterWhere(['like', 'toll_location', $this->toll_location]);

        return $dataProvider;
    }

}
