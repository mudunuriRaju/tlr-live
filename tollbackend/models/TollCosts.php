<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_costs".
 *
 * @property integer $toll_cost_id
 * @property integer $toll_id
 * @property integer $vechical_types_id
 * @property double $single_trip_cost
 * @property double $round_trip_cost
 * @property string $created_on
 * @property string $updated_on
 * @property string $updated_by
 *
 * @property TblPayments[] $tblPayments
 * @property TblTolls $toll
 * @property TblMasterVechicalTypes $vechicalTypes
 */
class TollCosts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_costs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_id', 'vechical_types_id', 'single_trip_cost', 'round_trip_cost', 'monthly_cost'], 'required'],
            [['toll_id', 'vechical_types_id'], 'integer'],
            [['single_trip_cost', 'round_trip_cost', 'monthly_cost', 'monthly_type_id', 'number_monthly_trips'], 'number'],
            [['created_on', 'updated_on', 'number_monthly_trips'], 'safe'],
            [['updated_by'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_cost_id' => 'Toll Cost ID',
            'toll_id' => 'Toll ID',
            'vechical_types_id' => 'Vechical Types ID',
            'single_trip_cost' => 'Single Trip Cost',
            'round_trip_cost' => 'Round Trip Cost',
            'monthly_cost' => 'Monthly Cost',
            'monthly_type_id' => 'Monthly Type',
            'number_monthly_trips' => 'Monthly Trips',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPayments()
    {
        return $this->hasMany(TblPayments::className(), ['toll_cost_id' => 'toll_cost_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasterVechicalTypes()
    {
        return $this->hasOne(MasterVechicalTypes::className(), ['vechical_types_id' => 'vechical_types_id']);
    }

    public function getMonthlytypes()
    {
        return $this->hasMany(MonthlyTypes::className(), ['monthly_type_id' => 'monthly_type_id']);
    }
}
