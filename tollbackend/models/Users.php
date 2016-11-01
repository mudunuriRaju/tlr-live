<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_users".
 *
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $user_email
 * @property string $password
 * @property integer $user_type_id
 * @property string $access_token
 * @property integer $status
 * @property string $created_on
 * @property string $expiry_date
 * @property integer $language_id
 * @property double $amount
 *
 * @property TblTrip[] $tblTrips
 * @property TblUserDetails[] $tblUserDetails
 * @property TblMasterLanguage $language
 * @property TblVechicalDetails[] $tblVechicalDetails
 * @property TblWalletTransations[] $tblWalletTransations
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'user_email', 'password', 'language_id'], 'required'],
            [['user_type_id', 'status', 'language_id'], 'integer'],
            [['created_on', 'expiry_date'], 'safe'],
            [['amount'], 'number'],
            [['firstname', 'lastname'], 'string', 'max' => 45],
            [['user_email'], 'string', 'max' => 256],
            [['password', 'access_token'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'user_email' => 'User Email',
            'password' => 'Password',
            'user_type_id' => 'User Type ID',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'created_on' => 'Created On',
            'expiry_date' => 'Expiry Date',
            'language_id' => 'Language ID',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTrips()
    {
        return $this->hasMany(TblTrip::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblUserDetails()
    {
        return $this->hasMany(TblUserDetails::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(TblMasterLanguage::className(), ['lagunage_id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblVechicalDetails()
    {
        return $this->hasMany(TblVechicalDetails::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblWalletTransations()
    {
        return $this->hasMany(TblWalletTransations::className(), ['user_id' => 'user_id']);
    }
}
