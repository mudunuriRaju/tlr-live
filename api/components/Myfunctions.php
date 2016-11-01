<?php
/**
 * Created by PhpStorm.
 * User: kesavam
 * Date: 24/6/15
 * Time: 5:50 PM
 */

namespace api\components;

use Yii;
use yii\base\Component;
use api\models\VechicalDetails;
use backend\models\TollDetails;
use api\models\TollCosts;
use api\models\UserMonthlyTolls;
use api\models\WalletTransactions;
use api\models\Payments;

class Myfunctions extends Component
{

    function distance($lat1, $lon1, $lat2, $lon2, $unit = "K")
    {

        $theta = $lon1 - $lon2;

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));

        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function create_monthly($params)
    {
        $vechical = VechicalDetails::find()->where(['user_id' => $params['user_id'], 'vechical_id' => $params['vechical_id']])->one();
        if ($vechical) {
            $tolls = json_decode($params['toll_id']);
            //print_r($tolls); exit;
            $total = 0;
            $params['valid_till'] = date("Y-m-d", strtotime($params['valid_from'] . ' + 30 days'));
            $total = 0;
            $error = false;
            foreach ($tolls as $key => $value) {
                $monthlycost = TollCosts::findOne(['toll_id' => $value->toll_id, 'vechical_types_id' => VechicalDetails::findOne(['vechical_id' => $params['vechical_id']])->vechical_type_id]);
                if ($monthlycost) {
                    $model = $this->sort_monthly_pass($monthlycost, $value, $params);
                    //print_r($model); exit;
                    /*$model = new UserMonthlyTolls();
                    $monthly = $model->findOne(['user_id' => $params['user_id'], 'toll_id' => $value->toll_id, 'vechical_id' => $params['vechical_id']]);
                    if (!empty($monthly)) {
                        $params['valid_till'] = date("Y-m-d", strtotime($monthly->valid_till. ' + 31 days'));
                        $output = $model->updateAll(['valid_from' => date('Y-m-d'), 'valid_till' => $params['valid_till']], ['user_monthly_tolls_id' => $monthly->user_monthly_tolls_id]);
                        $output = $model->findOne(['user_monthly_tolls_id' => $monthly->user_monthly_tolls_id]);
                    } else {
                        $param = $params;
                        $param['toll_id'] = $value->toll_id;
                        $model->attributes = $param;
                        $model->save();
                        $output = $model->findOne(['user_monthly_tolls_id' => $model->user_monthly_tolls_id]);
                    }*/
                    $payments[] = [$model->user_monthly_tolls_id, $monthlycost->toll_cost_id, $monthlycost->monthly_cost, 1, 3, 1, date('Y-m-d H:i:s')];
                    $total = $total + $monthlycost->monthly_cost;
                } else {
                    $no_pass[]['toll_id'] = $value->toll_id;
                    $output['Error'] = ['Message' => 'This tolls donot have monthly pass', 'No Pass' => $no_pass];
                }
            }

            if ($total != 0) {
                $payme = Yii::$app->db->createCommand()->batchInsert(Payments::tableName(), ['trip_id', 'toll_cost_id', 'amount', 'status_payed', 'trip_cost_type', 'pass_type', 'created_on'], $payments)->execute();
                $wmodel = new WalletTransactions();
                $wmodel->attributes = ['user_id' => $params['user_id'], 'transation_id' => $params['transation_id'], 'amount' => $total, 'transation_type' => 30, 'created_on' => date('Y-m-d H:i:s')];
                if ($payme && $wmodel->save()) {
                    $mopass = UserMonthlyTolls::find()->joinWith(['toll', 'vechicaldetails'])->where(['tbl_user_monthly_tolls.user_id' => $params['user_id']])->all();
                    $mopass = $this->monthly_pass($mopass);
                    $output = ['Code' => 200, 'Info' => $mopass];

                } else {
                    $output = ['Code' => 489, 'Error' => 'Something went wrong try again'];
                }
            }
        } else {
            $output = ['Code' => 488, 'Error' => 'Vechical input is wrong'];
        }
        return $output;
    }

    public function monthly_pass($data)
    {
        $data_array = [];
        $i = 0;
        foreach ($data as $key => $value) {
            if (!empty($value->vechicaldetails)) {
                $data_array[$i]['toll_id'] = $value->toll_id;
                $data_array[$i]['valid_from'] = $value->valid_from;
                $data_array[$i]['valid_till'] = $value->valid_till;
                $data_array[$i]['toll_name'] = $value->toll->toll_name;
                $data_array[$i]['toll_location'] = $value->toll->toll_location;
                $data_array[$i]['registration_no'] = $value->vechicaldetails->registration_no;
                $i++;
            }
        }

        return $data_array;
    }

    public function sort_monthly_pass($monthlycost, $value, $param)
    {

        $model = new UserMonthlyTolls();
        $monthly = $model->findOne(['user_id' => $param['user_id'], 'toll_id' => $value->toll_id, 'vechical_id' => $param['vechical_id']]);
        //$monthly = $model->findOne(['user_id' => 1, 'toll_id' => 1, 'vechical_id' => '1231as']);

        $type = 'mp' . $monthlycost->monthly_type_id;
        $set = $this->$type($param, $monthlycost, $monthly);
        if (!empty($monthly)) {
            $output = $model->updateAll($set, ['user_monthly_tolls_id' => $monthly->user_monthly_tolls_id]);
            $output = $model->findOne(['user_monthly_tolls_id' => $monthly->user_monthly_tolls_id]);
        } else {
            //print_r($set);
            $set['toll_id'] = $value->toll_id;
            $model->attributes = $set;
            $model->save();
            $output = $model;
            //print_r($model);
            //$output = $model->findOne(['user_monthly_tolls_id' => $model->user_monthly_tolls_id]);
        }

        return $output;
    }

    private function mp1($param, $monthlycost, $monthly = 0)
    {
        return ['Code' => 479, 'Error' => 'NA'];
    }

    private function mp2($param, $monthlycost, $monthly = 0)
    {
        if ($monthly) {
            $set['valid_till'] = date("Y-m-d", strtotime($monthly->valid_till . ' + 31 days'));
        } else {
            $set = $param;
            $set['valid_till'] = date("Y-m-d", strtotime(date('Y-m-d') . ' + 31 days'));
        }
        $set['valid_from'] = date("Y-m-d");
        return $set;
    }

    private function mp3($param, $monthlycost, $monthly = 0)
    {
        if ($monthly) {
            $set['valid_from'] = date("Y-m-d", strtotime('first day of this month'));
            if (date('Y-m-d') < date('Y-m-d', strtotime($monthly->valid_till))) {
                $set['valid_from'] = $monthly->valid_from;
            }
        } else {
            $set = $param;
            $set['valid_from'] = date("Y-m-d", strtotime('first day of this month'));
            $set['valid_till'] = date("Y-m-d", strtotime('last day of this month'));
        }
        return $set;
    }

    private function mp4($param, $monthlycost, $monthly = 0)
    {
        if (!$monthly) {
            $set = $param;
        }
        $set['valid_from'] = date("Y-m-d");
        $set['valid_till'] = date("Y-m-d", strtotime(date('Y-m-d') . ' + 31 days'));
        $set['no_of_trips'] = $monthlycost->number_monthly_trips;
        return $set;
    }

    private function mp5($param, $monthlycost, $monthly = 0)
    {
        if ($monthly) {
            $set['no_of_trips'] = $monthlycost->number_monthly_trips;
            if (date('Y-m-d') < date('Y-m-d', strtotime($monthly->valid_till))) {
                $set['no_of_trips'] = $monthly->no_of_trips + $monthlycost->number_monthly_trips;
            }

        } else {
            $set = $param;
            $set['no_of_trips'] = $monthlycost->number_monthly_trips;
        }
        $set['valid_from'] = date("Y-m-d");
        $set['valid_till'] = date("Y-m-d", strtotime(date('Y-m-d') . ' + 31 days'));
        return $set;
    }

    private function mp6($param, $monthlycost, $monthly = 0)
    {
        if ($monthly) {
            $set['no_of_trips'] = $monthlycost->no_of_trips + $monthlycost->number_monthly_trips;
        } else {
            $set = $param;
            $set['no_of_trips'] = $monthlycost->number_monthly_trips;
        }
        $set['valid_from'] = date("Y-m-d");
        $set['valid_till'] = date("Y-m-d", strtotime(date('Y-m-d') . ' + 31 days'));
        return $set;
    }

    public function callAPI($apiURL, $requestParamList)
    {

        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestParamList);


        //Yii::$app->alog->userLog('a', [$apiURL, date('Y-m-d H:i:s'), json_encode($requestParamList)]);
        $jsonResponse = curl_exec($ch);
        return true;
    }


}