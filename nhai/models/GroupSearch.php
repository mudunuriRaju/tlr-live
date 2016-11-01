<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Groups;

/**
 * GroupsSearch represents the model behind the search form about `backend\models\Groups`.
 */
class GroupSearch extends Groups
{
    /**
     * @inheritdoc
     */

    // public $group_name;
    //public $toll_group_id;

    public function rules()
    {
        return [
            [['toll_group_id'], 'integer'],
            [['group_name'], 'safe'],

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
        $query = Groups::find();

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
            'toll_group_id' => $this->toll_group_id,
            'created_on' => $this->created_on,
        ]);

        $query->andFilterWhere(['like', 'group_name', $this->group_name]);


        return $dataProvider;
    }
}
