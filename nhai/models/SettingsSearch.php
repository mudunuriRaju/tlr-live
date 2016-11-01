<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Settings;


class SettingsSearch extends Settings
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['column1', 'column2', 'type'], 'safe'],
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
    public function search($params, $type)
    {

        $query = Settings::find();
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
            'column1' => $this->column1,
        ]);
        $query->andFilterWhere(['like', 'type', $type]);
        $query->andFilterWhere(['like', 'column1', $this->column1]);
        return $dataProvider;
    }

}
