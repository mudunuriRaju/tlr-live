<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_boothside".
 *
 * @property integer $boothside_id
 * @property string $boothside_towoards
 * @property integer $toll_id
 * @property string $created_on
 *
 * @property TblTolls $toll
 */
class TollBoothside extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_boothside';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['boothside_from', 'lat', 'lng'], 'required'],
            [['toll_id'], 'integer'],
            [['boothside_from', 'created_on'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'boothside_id' => 'Boothside ID',
            'boothside_from' => 'Boothside from',
            'toll_id' => 'Toll ID',
            'created_on' => 'Created On',
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
