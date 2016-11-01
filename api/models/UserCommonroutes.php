<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;

//use yii\web\IdentityInterface;
//use yii\helpers\Security;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $created_on
 * @property string $updated_on
 */
class UserCommonroutes extends ActiveRecord
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
            [
                ['common_route_id', 'destination1', 'destination2', 'destination_1_lat', 'destination_1_lng', 'destination_2_lat', 'destination_2_lng', 'polyline_overview', 'waypoints'], 'required'],
            [['route_distance', 'path'], 'safe']
        ];
    }

//    public function getUsers()
//    {
//        return $this->hasMany(Users::className(), ['language_id' => 'lagunage_id']);
//    }


}
