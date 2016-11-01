<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 3/9/2016
 * Time: 5:11 PM
 */

namespace api\models;

use Yii;

class CardTemp extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_card_temp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'card', 'type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'card' => 'Card Number',
            'type' => 'type',
        ];
    }
}