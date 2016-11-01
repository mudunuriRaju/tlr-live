<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_master_user_types".
 *
 * @property integer $user_type_id
 * @property string $type_name
 * @property integer $prioity
 *
 * @property TblFleetUsers[] $tblFleetUsers
 */
class MasterUserTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_master_user_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'prioity'], 'required'],
            [['prioity'], 'integer'],
            [['type_name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_type_id' => 'User Type ID',
            'type_name' => 'Type Name',
            'prioity' => 'Prioity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblFleetUsers()
    {
        return $this->hasMany(TblFleetUsers::className(), ['user_type_id' => 'user_type_id']);
    }
}
