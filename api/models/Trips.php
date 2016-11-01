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
class Trips extends ActiveRecord
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_trip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_on', 'trip_type', 'trip_id', 'route_type', 'route_id', 'route_points_type'], 'required'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // 'user_id' => 'ID',
            'from_location' => 'From Location',
            'to_location' => 'To Location',
            'travel_date' => 'Travel Date',
            'tripid' => 'Trip ID',
            'user_id' => 'User ID',
            'trip_type' => 'Trip_type',
            'registration_no' => 'Registration No',
            'vechical_type_id' => 'vechical_type_id',
            //'created_on' => 'Created On',
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

    public static function findByTripid($trip_id)
    {
        return static::findOne(['trip_id' => $trip_id]);
    }

    public function getTblTripdetails()
    {
        return $this->hasMany(Tripdetails::className(), ['trip_id' => 'trip_id']);
    }

    public function getTblVechicaltypes()
    {
        return $this->hasMany(MasterVechicalTypes::className(), ['vechical_types_id' => 'vechical_type_id']);
    }

    public function Triphistory($id)
    {
        return Yii::app()->db->createCommand("CALL TripHistory(:id)")
            ->queryAll(array(':id' => $id));
    }

}
