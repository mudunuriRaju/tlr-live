<?php

namespace api\models;

use backend\models\TollDetails;
use Yii;

/**
 * This is the model class for table "tbl_user_trip_toll_extra_payments".
 *
 * @property integer $user_id
 * @property string $trip_details_id
 * @property integer $amount
 * @property integer $type_payments
 * @property integer $pay_status
 */
class UserTripTollExtraPayments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user_trip_toll_extra_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'trip_details_id', 'amount', 'type_payments'], 'required'],
            [['user_id', 'amount', 'type_payments', 'pay_status'], 'integer'],
            [['trip_details_id', 'transation_id'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'trip_details_id' => 'Trip Details ID',
            'amount' => 'Amount',
            'type_payments' => 'Type Payments',
            'pay_status' => 'Pay Status',
        ];
    }

    public function getTripdetails()
    {
        return $this->hasOne(Tripdetails::className(), ['trip_details_id' => 'trip_details_id']);
    }

    public function getToll()
    {
        return TollDetails::toll;
    }
}
