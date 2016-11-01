<?php

namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MasterLanguage;

/**
 * Laguage represents the model behind the search form about `backend\models\MasterLanguage`.
 */
class Laguage extends MasterLanguage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lagunage_id', 'status'], 'integer'],
            [['laguage_name', 'short'], 'safe'],
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
        $query = MasterLanguage::find();

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
            'lagunage_id' => $this->lagunage_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'laguage_name', $this->laguage_name])
            ->andFilterWhere(['like', 'short', $this->short]);

        return $dataProvider;
    }
}
