<?php

namespace tollbackend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;

/**
 * This is the model class for table "tbl_toll_users".
 *
 * @property integer $toll_user_id
 * @property string $toll_employee_id
 * @property string $toll_password
 * @property integer $toll_id
 * @property integer $toll_user_type_id
 * @property integer $status
 * @property integer $group_id
 * @property integer $language_id
 *
 * @property TblPayments[] $tblPayments
 * @property TblMasterLanguage $language
 * @property TblMasterTollUserTypes $tollUserType
 * @property TblTolls $toll
 * @property TblTollGroups $group
 */
class TollUsers extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_employee_id', 'toll_password', 'toll_id', 'toll_user_type_id', 'status', 'language_id'], 'required'],
            [['toll_employee_id', 'toll_password', 'toll_id', 'toll_user_type_id', 'status', 'language_id'], 'filter', 'filter' => 'trim'],
            ['toll_employee_id', 'in', 'not' => true, 'range' => TollUsers::find()->select('toll_employee_id')->asArray()->column(), 'message' => 'This Toll Employee Id is already in use', 'on' => 'create'],
            [['toll_id', 'toll_user_type_id', 'status', 'group_id', 'language_id'], 'integer'],
            [['toll_employee_id'], 'string', 'max' => 45],
            [['toll_password', 'toll_password_hash'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_user_id' => 'Toll User ID',
            'toll_employee_id' => 'Toll Employee ID',
            'toll_password' => 'Toll Password',
            'toll_password_hash' => 'Toll Password Hash',
            'toll_id' => 'Toll ID',
            'toll_user_type_id' => 'Toll User Type ID',
            'status' => 'Status',
            'group_id' => 'Group ID',
            'language_id' => 'Language ID',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        //return static::findOne(['toll_user_id' => $id, 'status' => self::STATUS_ACTIVE]);
        //echo 'sasa'; exit;
        //print_r($id); exit;
        $id = explode('_', $id);

        if (!empty($id[1]) && $id[1] === 'tolluser') {
            return static::findOne(['toll_user_id' => $id[0], 'status' => self::STATUS_ACTIVE]);
        } else {
            return static::findOne(['toll_user_id' => $id[0], 'status' => self::STATUS_DELETED]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPayments()
    {
        return $this->hasMany(TblPayments::className(), ['toll_user_id' => 'toll_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(MasterLanguage::className(), ['lagunage_id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollUserType()
    {
        return $this->hasOne(MasterTollUserTypes::className(), ['toll_user_type_id' => 'toll_user_type_id']);
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
    public function getGroup()
    {
        return $this->hasOne(TblTollGroups::className(), ['toll_group_id' => 'group_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['toll_employee_id' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey() . '_' . 'tolluser';
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        //return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->toll_password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function isUserAdmin($username)
    {
        if (static::findOne(['toll_employee_id' => $username])) {

            return true;
        } else {

            return false;
        }

    }
}
