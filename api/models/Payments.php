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
class Payments extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trip_id', 'toll_cost_id', 'amount', 'status_payed', 'trip_cost_type', 'booth_id', 'toll_user_id', 'created_on'], 'required'],
            [['trip_details_id'], 'safe']
        ];
    }

//    public function getUsers()
//    {
//        return $this->hasMany(Users::className(), ['language_id' => 'lagunage_id']);
//    }
    public static function findBytollid($toll_id, $trip_id)
    {
        return static::findAll(['trip_id' => $trip_id, 'toll_id' => $toll_id]);

    }

    public static function findByTripid($trip_id)
    {
        return static::findAll(['trip_id' => $trip_id, '!=', 'status', 2]);
    }
}
