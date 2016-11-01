<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_master_toll_user_types".
 *
 * @property integer $toll_user_type_id
 * @property string $type_name
 * @property integer $prioity
 *
 * @property TblTollUsers[] $tblTollUsers
 */
class MasterTollUserTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_master_toll_user_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_user_type_id', 'type_name', 'prioity'], 'required'],
            [['toll_user_type_id', 'prioity'], 'integer'],
            [['type_name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_user_type_id' => 'Toll User Type ID',
            'type_name' => 'Type Name',
            'prioity' => 'Prioity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTollUsers()
    {
        return $this->hasMany(TblTollUsers::className(), ['toll_user_type_id' => 'toll_user_type_id']);
    }
}
