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
class UserDetails extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'address_name', 'address_type'], 'required'],
            [['address2', 'address1', 'zipcode', 'city'], 'safe'],
            [['mobile', 'address_type', 'lat', 'lng'], 'number'],
            [['state', 'city', 'phone'], 'string', 'max' => 256],
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['language_id' => 'lagunage_id']);
    }


}
