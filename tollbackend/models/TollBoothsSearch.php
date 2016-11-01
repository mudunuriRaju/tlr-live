<?php

namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tollbackend\models\TollBooths;

/**
 * TollBoothsSearch represents the model behind the search form about `tollbackend\models\TollBooths`.
 */
class TollBoothsSearch extends TollBooths
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['booth_id', 'toll_id'], 'integer'],
            [['booth_unique_id', 'created_on', 'toll.toll_unique_number', 'boothside.boothside_from'], 'safe'],
        ];
    }

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['toll.toll_unique_number'], ['boothside.boothside_from']);
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
        $query = TollBooths::find();
        $query->joinWith(['toll']);
        $query->joinWith(['boothside']);
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $query->where(['tbl_tolls.group_id' => $user->group_id]);
        } else {
            $query->where(['tbl_tolls.toll_id' => $user->toll_id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['toll.toll_unique_number'] = [
            'asc' => ['toll.toll_unique_number' => SORT_ASC],
            'desc' => ['toll.toll_unique_number' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['boothside.boothside_from'] = [
            'asc' => ['boothside.boothside_from' => SORT_ASC],
            'desc' => ['boothside.boothside_from' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'booth_id' => $this->booth_id,
            'toll_id' => $this->toll_id,
        ]);

        $query->andFilterWhere(['like', 'toll_unique_number', $this->getAttribute('toll.toll_unique_number')]);
        $query->andFilterWhere(['like', 'toll_unique_number', $this->getAttribute('boothside.boothside_from')]);

        $query->andFilterWhere(['like', 'booth_unique_id', $this->booth_unique_id])
            ->andFilterWhere(['like', 'created_on', $this->created_on]);

        return $dataProvider;
    }
}
