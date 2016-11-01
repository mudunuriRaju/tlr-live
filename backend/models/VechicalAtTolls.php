<?php

namespace backend\models;

use Yii;
use yii\db\Command;

/**
 * This is the model class for table "tbl_vechical_at_tolls".
 *
 * @property integer $vechical_at_toll_id
 * @property integer $toll_id
 * @property integer $vechical_type_id_1
 * @property integer $vechical_type_id_2
 * @property integer $vechical_type_id_3
 * @property integer $vechical_type_id_4
 * @property integer $vechical_type_id_5
 * @property integer $vechical_type_id_6
 * @property integer $vechical_type_id_7
 *
 * @property TblTolls $toll
 */
class VechicalAtTolls extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_vechical_at_tolls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vechical_at_toll_id', 'toll_id'], 'required'],
            [['vechical_at_toll_id', 'toll_id', 'vechical_type_id_1', 'vechical_type_id_2', 'vechical_type_id_3', 'vechical_type_id_4', 'vechical_type_id_5', 'vechical_type_id_6', 'vechical_type_id_7'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vechical_at_toll_id' => 'Vechical At Toll ID',
            'toll_id' => 'Toll ID',
            'vechical_type_id_1' => 'Vechical Type Id 1',
            'vechical_type_id_2' => 'Vechical Type Id 2',
            'vechical_type_id_3' => 'Vechical Type Id 3',
            'vechical_type_id_4' => 'Vechical Type Id 4',
            'vechical_type_id_5' => 'Vechical Type Id 5',
            'vechical_type_id_6' => 'Vechical Type Id 6',
            'vechical_type_id_7' => 'Vechical Type Id 7',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    public function addColumn($vechical_types_id)
    {
        return $this->db->createCommand()->addColumn('tbl_vechical_at_tolls', "vechical_type_id_" . $vechical_types_id, 'BIGINT(20) NOT NULL DEFAULT 0')->execute();

    }
}
