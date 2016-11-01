<?php

namespace api\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $created_on
 * @property string $updated_on
 */
class Tripdetails extends ActiveRecord
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_trip_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trip_details_id', 'trip_id', 'toll_id', 'created_on', 'trip_type', 'vechical_id', 'vechical_type'], 'required'],
            [['direction_id'], 'safe'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // 'user_id' => 'ID',
            'trip_id' => 'Trip Id',
            'toll_id' => 'Toll Id',
            'created_on' => 'Travel Date',
            'trip_type' => 'Trip_type',
            'Direction' => 'Direction Id'
            // 'updated_on' => 'Updated Date',

            // 'created_on' => 'Created On',
            // 'updated_on' => 'Updated On',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);

        return $fields;
    }


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['user_email' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


    public function getTblTrips()
    {
        return $this->hasMany(Trips::className(), ['trip_id' => 'trip_id']);
    }

    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    public function getTblPayments()
    {
        return $this->hasMany(Payments::className(), ['trip_id' => 'trip_details_id']);
    }

    public function getExtrapayments()
    {
        return $this->hasOne(UserTripTollExtraPayments::className(), ['trip_details_id' => 'trip_details_id']);
    }

    public function getTollCost()
    {
        return $this->hasOne(TollCosts::className(), ['toll_id' => 'toll_id']);
    }

    public function getVehicaldetails()
    {
        return $this->hasone(VechicalDetails::className(), ['vechical_id' => 'vechical_id']);
    }


}
