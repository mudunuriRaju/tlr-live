<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ReferencePoints;

/**
 * ReferencePointsSearch represents the model behind the search form about `backend\models\ReferencePoints`.
 */
class ReferencePointsSearch extends ReferencePoints
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_id', 'toll_ref_point_id', 'direction_id'], 'integer'],
            [['toll_axis', 'lat', 'lng'], 'safe'],
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
        $query = ReferencePoints::find();
        $query->joinWith(['boothside']);

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
            'tbl_toll_reference_points.toll_id' => $this->toll_id,
            'toll_ref_point_id' => $this->toll_ref_point_id,
        ]);

        $query->andFilterWhere(['like', 'toll_axis', $this->toll_axis])
            ->andFilterWhere(['like', 'direction_id', $this->getAttribute('boothside.boothside_from')])
            ->andFilterWhere(['like', 'lat', $this->lat])
            ->andFilterWhere(['like', 'lng', $this->lng]);

        return $dataProvider;
    }
}
