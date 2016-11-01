<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Settings;

class SettingsSearch extends Settings
{
    public function rules()
    {
        return [
            [['column1', 'column2', 'type'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $type)
    {
        $query = Settings::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
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
