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
class HistoryOfPayments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_history_of_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['history_payment_id', 'toll_id','amount','date','vehical_type','counter'], 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'history_payment_id' => 'Vechical At Toll ID',
            'toll_id' => 'Toll ID',
            'amount' => 'Amount',
            'vehical_type' => 'vehical_type',
            'counter' => 'counter',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }


}
