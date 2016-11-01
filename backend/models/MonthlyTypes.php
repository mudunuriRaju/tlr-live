<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_monthly_cost_types".
 *
 * @property integer $monthly_type_id
 * @property string $type_name
 * @property string $type_description
 * @property string $status
 *
 * @property TblTollCosts[] $tblTollCosts
 * @property TblVechicalDetails[] $tblVechicalDetails
 */
class MonthlyTypes extends \yii\db\ActiveRecord
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
            [['monthly_type_id', 'type_name'], 'required'],
            [['type_description'], 'string'],
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
            'type_description' => 'type_description',
        ];
    }

}
