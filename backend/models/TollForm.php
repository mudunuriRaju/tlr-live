<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Tolls;
use backend\models\TollUsers;
use backend\models\TollDetails;
use yii\validators;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TollForm
 *
 * @author kesavam
 */
class TollForm extends Tolls
{

    //put your code here
    public $toll_employee_id;
    public $toll_password;
    public $status;
    public $toll_user_type_id;
    public $language_id;
    public $toll_unique_number, $toll_location, $toll_lat, $toll_lng, $toll_radius, $allowed_ip, $toll_name, $toll_stretch;
    public $toll_extra_details, $toll_concessionaire, $toll_address, $toll_contact, $toll_state;

    public function rules()
    {
        return [
            [['toll_unique_number', 'toll_location', 'toll_lat', 'toll_lng', 'toll_radius', 'toll_employee_id', 'toll_password', 'toll_id', 'toll_user_type_id', 'language_id', 'toll_name'], 'required'],
            [['toll_lat', 'toll_lng', 'toll_radius'], 'number'],
            [['group_id'], 'integer'],
            [['motorway_id', 'toll_unique_number'], 'string', 'max' => 45],
            [['allowed_ip', 'toll_location'], 'string', 'max' => 256],
            ['toll_unique_number', 'in', 'not' => true, 'range' => Tolls::find()->select('toll_unique_number')->asArray()->column(), 'message' => 'This toll unique number is already in use'],
        ];
    }

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
        ];
    }

    public function custom_function_validation($attribute, $params)
    {

        if (!in_array($this->$attribute, $params['toll_unique_number']))
            $this->addError($attribute, 'Custom Validation Error');

    }

    public function getTollUsers()
    {
        return $this->hasMany(TollUsers::className(), ['toll_id' => 'toll_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTollDetails()
    {
        return $this->hasOne(TollDetails::className(), ['toll_id' => 'toll_id']);
    }

}
