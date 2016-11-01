<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "tbl_user_route_selection".
 *
 * @property string $route_id
 * @property integer $user_id
 * @property string $nick_name
 * @property integer $route_type
 * @property integer $fav_type
 */
class UserRouteSelection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user_route_selection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route_id'], 'required'],
            [['user_id', 'nick_name', 'trip_id'], 'safe'],
            [['user_id', 'route_type', 'fav_type'], 'integer'],
            [['route_id', 'nick_name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'route_id' => 'Route ID',
            'user_id' => 'User ID',
            'nick_name' => 'Nick Name',
            'route_type' => 'Route Type',
            'fav_type' => 'Fav Type',
        ];
    }
}
