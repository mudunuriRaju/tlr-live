<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "tbl_common_routes".
 */
class CommonRoutes extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_common_routes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['destination1', 'destination2'], 'required'],
            [['destination_1_lat', 'destination_2_lat', 'destination_1_lng', 'destination_2_lng', 'waypoints'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'common_route_id' => 'Route Id',
            'destination1' => 'From Location',
            'destination2' => 'To Location',
            'destination_1_lat' => 'From Lat',
            'destination_2_lat' => 'To Lat',
            'destination_1_lng' => 'From Lng',
            'destination_2_lng' => 'To Lng',
            'waypoints' => 'Waypoints'

        ];
    }

}
