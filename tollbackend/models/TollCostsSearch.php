<?php
namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tollbackend\models\TollCosts;
use tollbackend\models\TollUsers;


/**
 * TollCostsSearch represents the model behind the search form about `tollbackend\models\TollCosts`.
 */
class TollCostsSearch extends TollCosts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['toll.toll_unique_number', 'masterVechicalTypes.type'], 'safe'],
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
        return array_merge(parent::attributes(), ['toll.toll_unique_number'], ['masterVechicalTypes.type']);
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
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        $query = TollCosts::find();
        $query->joinWith(['toll']);
        $query->joinWith(['masterVechicalTypes']);
        $query->joinWith(['monthlytypes']);

        if (!empty($user->group_id)) {
            $query->where(['tbl_tolls.group_id' => $user->group_id]);
        } else {
            $query->where(['tbl_tolls.toll_id' => $user->toll_id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['toll.toll_unique_number'] = [
            'asc' => ['tbl_tolls.toll_unique_number' => SORT_ASC],
            'desc' => ['tbl_tolls.toll_unique_number' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['masterVechicalTypes.type'] = [
            'asc' => ['tbl_master_vechical_types.type' => SORT_ASC],
            'desc' => ['tbl_master_vechical_types.type' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['monthlytypes.monthly_type_id'] = [
            'asc' => ['tbl_monthly_cost_types.monthly_type_id' => SORT_ASC],
            'desc' => ['tbl_monthly_cost_types.monthly_type_id' => SORT_DESC],
        ];


        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'toll_cost_id' => $this->toll_cost_id,
            'toll_id' => $this->toll_id,
            'vechical_types_id' => $this->vechical_types_id,
            'single_trip_cost' => $this->single_trip_cost,
            'round_trip_cost' => $this->round_trip_cost,
            'monthly_cost' => $this->monthly_cost,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
        ]);
        $query->andFilterWhere(['like', 'toll_unique_number', $this->getAttribute('toll.toll_unique_number')]);
        $query->andFilterWhere(['like', 'type', $this->getAttribute('masterVechicalTypes.type')]);
        $query->andFilterWhere(['like', 'updated_by', $this->updated_by]);
        $query->andFilterWhere(['like', 'toll_id', $this->getAttribute('toll.toll_id')]);
        return $dataProvider;
    }
}
