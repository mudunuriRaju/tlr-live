<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_reference_points".
 *
 * @property integer $toll_id
 * @property string $toll_axis
 * @property string $lat
 * @property string $lng
 * @property integer $toll_ref_point_id
 */
class ReferencePoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_reference_points';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toll_id', 'direction_id'], 'required'],
            [['toll_id'], 'integer'],
            [['toll_axis', 'lat', 'lng'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_id' => 'Toll ID',
            'direction_id' => 'Direction',
            'toll_axis' => 'Toll Axis',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'toll_ref_point_id' => 'Toll Ref Point ID',
        ];
    }

    public function getBoothside()
    {
        return $this->hasOne(TollBoothside::className(), ['boothside_id' => 'direction_id']);
    }
}
