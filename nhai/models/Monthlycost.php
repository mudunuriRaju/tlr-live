<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_monthly_cost_types".
 */
class Monthlycost extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monthly_cost_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'type_description'], 'required'],
            [['type_name', 'type_description'], 'string'],
            [['status'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'monthly_type_id' => 'monthly_type_id',
            'type_name' => 'Type',
            'type_description' => 'Description',
            'status' => 'Status',
        ];
    }

}
