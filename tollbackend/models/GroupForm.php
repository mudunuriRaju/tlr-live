<?php

namespace tollbackend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tollbackend\models\Groups;
use tollbackend\models\GroupSearch;
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
class GroupForm extends Groups
{

    //put your code here
    public $group_name;
    public $toll_group_id;

    public function rules()
    {
        return [
            [['toll_group_id', 'group_name'], 'required'],
            [['group_name'], 'string', 'max' => 256],
            ['group_name', 'in', 'not' => true, 'range' => Groups::find()->select('group_name')->asArray()->column(), 'message' => 'This toll group name is already in use'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'toll_group_id' => 'Toll Group ID',
            'group_name' => 'Group Name',

        ];
    }

}
