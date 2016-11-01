<?php

namespace api\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\base\NotSupportedException;
use yii\base\Model;

/**
 * This is the model class for table "tbl_tolls".
 *
 * @property integer $toll_id
 * @property string $toll_unique_number
 * @property string $toll_location
 * @property double $toll_lat
 * @property double $toll_lng
 * @property double $toll_radius
 * @property string $motorway_id
 * @property double $amount
 * @property string $allowed_ip
 * @property integer $group_id
 * @property string $toll_status
 * @property string $place_id
 *
 * @property TblTollBooths[] $tblTollBooths
 * @property TblTollBoothside[] $tblTollBoothsides
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
            [['toll_unique_number', 'toll_location', 'toll_lat', 'toll_lng', 'toll_radius', 'allowed_ip'], 'required'],
            [['toll_lat', 'toll_lng', 'toll_radius', 'amount'], 'number'],
            [['group_id'], 'integer'],
            [['toll_unique_number', 'motorway_id'], 'string', 'max' => 45],
            [['toll_location', 'allowed_ip'], 'string', 'max' => 256],
            [['toll_status'], 'string', 'max' => 2],
            [['place_id'], 'string', 'max' => 512],
            [['toll_unique_number'], 'unique']
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
            'toll_status' => 'Toll Status',
            'place_id' => 'Place ID',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['amount'], $fields['allowed_ip'], $fields['group_id'], $fields['toll_status'], $fields['place_id']);

        return $fields;
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
    public function getTblTollBoothsides()
    {
        return $this->hasMany(TblTollBoothside::className(), ['toll_id' => 'toll_id']);
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
    public function getTblTollDetails()
    {
        return $this->hasOne(TblTollDetails::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTollUsers()
    {
        return $this->hasMany(TblTollUsers::className(), ['toll_id' => 'toll_id']);
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
