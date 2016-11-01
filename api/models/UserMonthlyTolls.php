<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\base\NotSupportedException;


class UserMonthlyTolls extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user_monthly_tolls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'toll_id', 'valid_from', 'valid_till', 'vechical_id'], 'required'],
            [['user_id', 'toll_id'], 'number'],
            [['created_on'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'user_id',
            'toll_id' => 'toll_id',
            'valid_from' => 'valid_from',
            'valid_till' => 'valid_till',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        //unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);

        return $fields;
    }

    public function getToll()
    {
        return $this->hasOne(Tolls::className(), ['toll_id' => 'toll_id']);
    }

    public function getVechicaldetails()
    {
        return $this->hasOne(VechicalDetails::className(), ['vechical_id' => 'vechical_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */


}
