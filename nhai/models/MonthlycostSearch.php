<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Monthlycost;

/**
 * Laguage represents the model behind the search form about `backend\models\MasterLanguage`.
 */
class MonthlycostSearch extends Monthlycost
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'type_description'], 'safe'],
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
        $query = Monthlycost::find();
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
            'type_name' => $this->type_name,
        ]);

        $query->andFilterWhere(['like', 'type_name', $this->type_name]);
        return $dataProvider;
    }

}
