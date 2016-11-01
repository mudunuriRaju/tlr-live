<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CommonRoutes;

/**
 * Laguage represents the model behind the search form about `backend\models\MasterLanguage`.
 */
class CommonRoutesSearch extends CommonRoutes
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['destination1', 'destination2'], 'safe'],
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
        $query = CommonRoutes::find();
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
            'destination1' => $this->destination1,
        ]);

        $query->andFilterWhere(['like', 'destination1', $this->destination1]);
        return $dataProvider;
    }

}
