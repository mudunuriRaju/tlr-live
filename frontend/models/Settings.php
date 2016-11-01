<?php
namespace frontend\models;

use Yii;

class Settings extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tbl_settings';
    }

    public function rules()
    {
        return [
            [['column1', 'column2', 'type'], 'required'],
            [['column1', 'column2'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'column1' => 'Heading',
            'column2' => 'Answer'
        ];
    }
}
