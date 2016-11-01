<?php
/**
 * Created by PhpStorm.
 * User: kesavam
 * Date: 24/4/15
 * Time: 6:49 PM
 */

namespace tollbackend\models;

use yii\base\Model;
use Yii;
use tollbackend\models\TollUsers;
use tollbackend\models\Tolls;
use tollbackend\models\TollBoothside;


class TollSidesForm extends TollBoothside
{

    public $towords_1;
    public $towords_2;
    public $toll_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['towords_1', 'towords_2', 'toll_id'], 'required'],
            // rememberMe must be a boolean value

        ];
    }

    public function toll_between()
    {
        if ($this->validate()) {
            $booths = new TollBoothside();
            $booths->toll_id = $this->toll_id;
            $booths->boothside_towoards = $this->towords_1;
            if ($booths->save()) {
                $booths = new TollBoothside();
                $booths->toll_id = $this->toll_id;
                $booths->boothside_towoards = $this->towords_2;
                if ($booths->save()) {
                    return $booths;
                }
            }
        }

        return null;
    }
}