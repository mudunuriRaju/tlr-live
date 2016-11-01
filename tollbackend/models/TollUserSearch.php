<?php

namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tollbackend\models\TollUsers;

/**
 * TollUserSearch represents the model behind the search form about `tollbackend\models\TollUsers`.
 */
class TollUserSearch extends TollUsers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['toll_user_id', 'toll_id', 'toll_user_type_id', 'status', 'group_id', 'language_id'], 'integer'],
            [['toll_employee_id', 'toll.toll_unique_number', 'tollUserType.type_name'], 'safe'],
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
        return array_merge(parent::attributes(), ['toll.toll_unique_number'], ['tollUserType.type_name']);
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
        $query = TollUsers::find();
        $query->joinWith(['toll']);
        $query->joinWith(['tollUserType']);
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $query->where(['tbl_tolls.group_id' => $user->group_id]);
        } else {
            $query->where(['tbl_tolls.toll_id' => $user->toll_id]);
        }
        $query->andWhere(['>', 'tbl_toll_users.toll_user_type_id', $user->toll_user_type_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['toll.toll_unique_number'] = [
            'asc' => ['toll.toll_unique_number' => SORT_ASC],
            'desc' => ['toll.toll_unique_number' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['tollUserType.type_name'] = [
            'asc' => ['tollUserType.type_name' => SORT_ASC],
            'desc' => ['tollUserType.type_name' => SORT_DESC],
        ];
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'toll_user_id' => $this->toll_user_id,
            'toll_id' => $this->toll_id,
            'toll_user_type_id' => $this->toll_user_type_id,
            'status' => $this->status,
            'group_id' => $this->group_id,
            'language_id' => $this->language_id,
        ]);


        $query->andFilterWhere(['like', 'toll_unique_number', $this->getAttribute('toll.toll_unique_number')]);
        $query->andFilterWhere(['like', 'type_name', $this->getAttribute('tollUserType.type_name')]);

        $query->andFilterWhere(['like', 'toll_employee_id', $this->toll_employee_id])
            ->andFilterWhere(['like', 'toll_password', $this->toll_password]);


        return $dataProvider;
    }
}
