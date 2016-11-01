<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_tolls".
 *
 * @property integer $toll_id
 * @property string $toll_unique_number
 * @property string $toll_location
 * @property double $toll_lat
 * @property double $toll_lng
 * @property double $toll_radius
 * @property integer $motorway_id
 * @property double $amount
 * @property string $allowed_ip
 * @property integer $group_id
 *
 * @property TblTollBooths[] $tblTollBooths
 * @property TblTollCosts[] $tblTollCosts
 * @property TblTollDetails $tblTollDetails
 * @property TblTollUsers[] $tblTollUsers
 * @property TblTollGroups $group
 * @property TblTripDetails[] $tblTripDetails
 */
class Tolls extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tolls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_unique_number', 'toll_location', 'toll_lat', 'toll_lng', 'toll_radius', 'toll_name'], 'required'],
            [['toll_lat', 'toll_lng', 'toll_radius', 'amount'], 'number'],
            ['toll_unique_number', 'unique'],
            [['group_id', 'toll_status'], 'integer'],
            [['motorway_id', 'toll_unique_number', 'toll_name'], 'string', 'max' => 45],
            [['allowed_ip', 'toll_location', 'toll_stretch'], 'string', 'max' => 256],
            ['place_id', 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_id' => 'Toll ID',
            'toll_unique_number' => 'Toll Unique Number',
            'toll_location' => 'Toll Location',
            'toll_lat' => 'Toll Lat',
            'toll_lng' => 'Toll Lng',
            'toll_radius' => 'Toll Radius',
            'motorway_id' => 'Motorway ID',
            'amount' => 'Amount',
            'allowed_ip' => 'Allowed Ip',
            'group_id' => 'Group ID',
            'place_id' => 'Google place id',
            'toll_status' => 'Status'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTollBooths()
    {
        return $this->hasMany(TblTollBooths::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollCosts()
    {
        return $this->hasMany(TollCosts::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollDetails()
    {
        return $this->hasOne(TollDetails::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollUsers()
    {
        return $this->hasMany(TollUsers::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVechicalAtTolls()
    {
        return $this->hasMany(VechicalAtTolls::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(TblTollGroups::className(), ['toll_group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTripDetails()
    {
        return $this->hasMany(TblTripDetails::className(), ['toll_id' => 'toll_id']);
    }

}
