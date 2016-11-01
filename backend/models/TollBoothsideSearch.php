<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tollbackend\models\TollBoothside;

/**
 * TollBoothsideSearch represents the model behind the search form about `tollbackend\models\TollBoothside`.
 */
class TollBoothsideSearch extends TollBoothside
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['boothside_id', 'toll_id'], 'integer'],
            [['boothside_from', 'created_on', 'toll.toll_unique_number'], 'safe'],
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

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['toll.toll_unique_number']);
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
        $query = TollBoothside::find();
        /*$query->joinWith('toll');
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if(!empty($user->group_id)){
            $query->where(['tbl_tolls.group_id' => $user->group_id]);
        }else {
            $query->where(['tbl_tolls.toll_id' => $user->toll_id]);
        }*/
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['toll.toll_unique_number'] = [
            'asc' => ['toll.toll_unique_number' => SORT_ASC],
            'desc' => ['toll.toll_unique_number' => SORT_DESC],
        ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'boothside_id' => $this->boothside_id,
            'toll_id' => $this->toll_id,
        ]);

        $query->andFilterWhere(['like', 'toll_unique_number', $this->getAttribute('toll.toll_unique_number')]);


        $query->andFilterWhere(['like', 'boothside_from', $this->boothside_from])
            ->andFilterWhere(['like', 'created_on', $this->created_on]);

        return $dataProvider;
    }
}
