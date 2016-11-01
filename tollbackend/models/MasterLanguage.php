<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_master_language".
 *
 * @property integer $lagunage_id
 * @property string $laguage_name
 * @property string $short
 * @property integer $status
 *
 * @property TblFleetUsers[] $tblFleetUsers
 * @property TblTollUsers[] $tblTollUsers
 * @property TblUsers[] $tblUsers
 */
class MasterLanguage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_master_language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['laguage_name', 'short'], 'required'],
            [['status'], 'integer'],
            [['laguage_name'], 'string', 'max' => 45],
            [['short'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lagunage_id' => 'Lagunage ID',
            'laguage_name' => 'Laguage Name',
            'short' => 'Short',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblFleetUsers()
    {
        return $this->hasMany(TblFleetUsers::className(), ['language_id' => 'lagunage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTollUsers()
    {
        return $this->hasMany(TblTollUsers::className(), ['language_id' => 'lagunage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['language_id' => 'lagunage_id']);
    }
}
