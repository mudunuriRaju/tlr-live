<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_details".
 *
 * @property integer $toll_details_id
 * @property integer $toll_id
 * @property string $toll_concessionaire
 * @property string $toll_address
 * @property string $toll_contact
 * @property string $toll_state
 * @property string $toll_extra_details
 * @property string $bank_address
 * @property string $bank_name
 * @property string $bank_location
 * @property string $bank_account
 * @property string $bank_ifsc
 * @property string $bank_swift
 *
 * @property TblTolls $toll
 */
class TollDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_id'], 'required'],
            [['toll_id'], 'integer'],
            [['toll_extra_details'], 'string'],
            [['toll_concessionaire'], 'string', 'max' => 145],
            [['toll_address'], 'string', 'max' => 255],
            [['toll_contact', 'toll_state', 'bank_address', 'bank_name', 'bank_location', 'bank_account', 'bank_ifsc', 'bank_swift'], 'string', 'max' => 45],
            [['toll_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_details_id' => 'Toll Details ID',
            'toll_id' => 'Toll ID',
            'toll_concessionaire' => 'Toll Concessionaire',
            'toll_address' => 'Toll Address',
            'toll_contact' => 'Toll Contact',
            'toll_state' => 'Toll State',
            'toll_extra_details' => 'Toll Extra Details',
            'bank_address' => 'Bank Address',
            'bank_name' => 'Bank Name',
            'bank_location' => 'Bank Location',
            'bank_account' => 'Bank Account',
            'bank_ifsc' => 'Bank Ifsc',
            'bank_swift' => 'Bank Swift',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(TblTolls::className(), ['toll_id' => 'toll_id']);
    }
}
