<?php

namespace tollbackend\models;

use Yii;

/**
 * This is the model class for table "tbl_toll_groups".
 *
 * @property integer $toll_group_id
 * @property string $group_name
 * @property string $group_key
 * @property string $created_on
 */
class Groups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_toll_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['group_name'], 'required']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'toll_group_id' => 'Toll Group ID',
            'group_name' => 'Group Name',

        ];
    }
}
