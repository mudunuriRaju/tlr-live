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
class UnregisteredVechicals extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_unregistered_vechicals';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'registration_no', 'vechical_type_id', 'created_on', 'vechical_id'], 'required'],];
    }

//    public function getUsers()
//    {
//        return $this->hasMany(Users::className(), ['language_id' => 'lagunage_id']);
//    }


}
