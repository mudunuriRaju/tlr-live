<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_settings".
 */
class Settings extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['column1', 'column2'], 'required'],
            [['column1', 'column2'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'column1' => 'Heading',
            'column2' => 'Answer'
        ];
    }

}
