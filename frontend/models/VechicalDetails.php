<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\base\NotSupportedException;


class VechicalDetails extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_vechical_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'registration_no', 'vechical_type_id', 'vechical_nickname'], 'required'],
            [['registration_no'], 'unique', 'on' => 'create'],
            [['user_id', 'vechical_type_id'], 'number'],
            [['vechical_registrated_under', 'vechical_make'], 'string']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'user_id',
            'registration_no' => 'registration_no',
            'vechical_type_id' => 'vechical_type_id',
            'vechical_registrated_under' => 'vechical_registrated_under',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        //unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);

        return $fields;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVechicalTypes()
    {
        return $this->hasMany(MasterVechicalTypes::className(), ['vechical_types_id' => 'vechical_type_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getVtype()
    {
        return $this->hasOne(MasterVechicalTypes::className(), ['vechical_types_id' => 'vechical_type_id']);
    }


}
