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
class WalletTransactions extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_wallet_transations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'transation_id', 'amount', 'created_on', 'transation_type'], 'required'],
            [['trip_id'], 'safe']

        ];
    }


}
