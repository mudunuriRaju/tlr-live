<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_booths".
 *
 * @property integer $booth_id
 * @property integer $toll_id
 * @property string $booth_unique_id
 * @property string $created_on
 *
 * @property TblPayments[] $tblPayments
 * @property TblTolls $toll
 */
class TollBooths extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_booths';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_id', 'booth_side'], 'required'],
            [['booth_unique_id', 'booth_side'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'booth_id' => 'Booth ID',
            'toll_id' => 'Toll ID',
            'booth_unique_id' => 'Booth Name',
            'booth_side' => 'Booth towords',
            'created_on' => 'Created On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPayments()
    {
        return $this->hasMany(TblPayments::className(), ['booth_id' => 'booth_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    public function getBoothside()
    {
        return $this->hasOne(TollBoothside::className(), ['boothside_id' => 'booth_side']);
    }
}
