<?php

namespace tollbackend\models;

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
class HistoryDateWithvechicaltypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_history_date_withvechicaltypes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['history_date_withvechicaltypes_id', 'toll_id', 'date'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'history_date_withvechicaltypes_id' => 'History ID',
            'toll_id' => 'Toll ID',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    public function addColumn($vechical_types_id){
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "counter_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "amount_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "counter_single_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "counter_double_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "counter_monthly_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "amount_single_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();
        return $this->db->createCommand()->addColumn('tbl_history_date_withvechicaltypes', "amount_double_".$vechical_types_id, 'FLOAT NULL DEFAULT 0')->execute();

    }
}
