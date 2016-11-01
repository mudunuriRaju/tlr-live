<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_users".
 *
 * @property integer $toll_user_id
 * @property string $toll_employee_id
 * @property string $toll_password
 * @property string $toll_password_hash
 * @property integer $toll_id
 * @property integer $toll_user_type_id
 * @property integer $status
 * @property integer $group_id
 * @property integer $language_id
 *
 * @property TblPayments[] $tblPayments
 * @property TblTollGroups $group
 * @property TblMasterLanguage $language
 * @property TblTolls $toll
 * @property TblMasterTollUserTypes $tollUserType
 */
class TollUsers extends \yii\db\ActiveRecord
{
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
     * @return \yii\db\ActiveQuery
     */
    public function getTblPayments()
    {
        return $this->hasMany(TblPayments::className(), ['toll_user_id' => 'toll_user_id']);
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
    public function getLanguage()
    {
        return $this->hasOne(TblMasterLanguage::className(), ['lagunage_id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(TblTolls::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollUserType()
    {
        return $this->hasOne(TblMasterTollUserTypes::className(), ['toll_user_type_id' => 'toll_user_type_id']);
    }
}
