<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "tbl_master_vechical_types".
 *
 * @property integer $vechical_types_id
 * @property string $type
 * @property string $description
 * @property string $status
 *
 * @property TblTollCosts[] $tblTollCosts
 * @property TblVechicalDetails[] $tblVechicalDetails
 */
class MasterVechicalTypes extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_master_vechical_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vechical_types_id' => 'Vechical Types ID',
            'type' => 'Type',
            'description' => 'Description',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTollCosts()
    {
        return $this->hasMany(TblTollCosts::className(), ['vechical_types_id' => 'vechical_types_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblVechicalDetails()
    {
        return $this->hasMany(TblVechicalDetails::className(), ['vechical_type_id' => 'vechical_types_id']);
    }

}
