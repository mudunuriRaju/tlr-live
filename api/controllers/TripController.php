<?php

namespace api\controllers;

use api\models\ReferencePoints;
use api\models\TollBoothside;
use api\models\Tolls;
use api\models\UnregisteredVechicals;
use api\models\UserDetails;
use api\models\Userroutes;
use api\models\UserRouteSelection;
use api\models\VechicalDetails;
use api\models\HistoryOfPayments;
use api\models\HistoryDateWithvechicaltypes;
use common\components\Toll;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use api\models\Trips;
use api\models\Tripdetails;
use yii\helpers\BaseStringHelper;
use yii\helpers\BaseJson;
use yii\web\UploadedFile;
use yii\helpers\Url;
use api\models\Userlogpath;
use api\models\TollCosts;
use api\models\User;
use api\models\WalletTransactions;
use api\models\UserMonthlyTolls;
use api\models\Payments;
use api\models\UserCommonroutes;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class TripController extends Controller
{

    private $succes = true;
    //public $modelClass = 'api\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //echo $this->action;
//        if (Yii::app()->controller->id != 'index') {
//        $behaviors['authenticator'] = [
//            'class' => HttpBasicAuth::className(),
//            'only' => ['view']
//        ];
//        }
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */

    public function actions()
    {
        if (Yii::$app->request->post('timezone')) {
            //echo Yii::$app->request->post('timezone'); exit;
            // echo urldecode(Yii::$app->request->post('timezone')); exit;
            date_default_timezone_set(urldecode(Yii::$app->request->post('timezone')));
        }
        date_default_timezone_set('asia/kolkata');
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
    }

    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        date_default_timezone_set('asia/kolkata');
        $date = strtotime(date('YmdHis'));
        $model = new Trips();
        $model->attributes = $params;
        $model->trip_waypoints = $params['waypoints'];
        if ($params['route_type'] == 1) {
            $route = $this->route_select($params);
            $model->route_id = $route['route_id'];
            $model->route_points_type = $route['route_points_type'];
        }
        $model->fav_type = 0;
        $trip_count = Trips::find()->where(['route_id' => $model->route_id, 'route_type' => $model->route_type, 'user_id' => $model->user_id, 'fav_type' => 1])->count();
        if ($trip_count > 0)
            $model->fav_type = 1;
        $vec_det = $this->vechical_details($params['vechical_type'], $params);

        if ($this->succes) {
            $vechical_id = $vec_det->vechical_id;
            //User::updateAll(['user_id' => $user_id],['amount' => $user->amount - $toll_cost->single_trip_cost]);
            $params['vechical_type_id'] = $vechical_type_id = $vec_det->vechical_type_id;
        } else {
            return ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        $model->trip_id = $date . '_' . $route['route_id'] . "_" . $params['user_id'];
        $model->created_on = date('Y-m-d H:i:s');
        if ($this->succes && $model->save()) {
            $params['route_id'] = $model->route_id;
            $trip = $this->walet_payment_details($model->trip_id, $vechical_id, $model->trip_type, $params);
            $amount = User::find()->where(['user_id' => $params['user_id']])->one();
            $output = ['Code' => 200, 'Info' => $trip, 'amount' => $amount->amount];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->tolluserLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionEdit()
    {
        $params = Yii::$app->request->post();
        date_default_timezone_set('asia/kolkata');
        $date = strtotime(date('YmdHis'));
        $model = Trips::findOne($params['trip_id']);
        print_r($model);
        exit;
        if ($params['route_type'] == 1) {
            $route = $this->route_select($params);
            //$model->route_id = $route['route_id'];
            //$model->route_points_type = $route['route_points_type'];
        }
        $vec_det = $this->vechical_details($params['vechical_type'], $params);
        if ($this->succes) {
            $vechical_id = $vec_det->vechical_id;
            //User::updateAll(['user_id' => $user_id],['amount' => $user->amount - $toll_cost->single_trip_cost]);
            $params['vechical_type_id'] = $vechical_type_id = $vec_det->vechical_type_id;
            $params['route_id'] = $route['route_id'];
            $trip = $this->walet_payment_details($params['trip_id'], $vechical_id, $params['trip_type'], $params);
            $amount = User::find()->where(['user_id' => $params['user_id']])->one();
            $output = ['Code' => 200, 'Info' => $trip, 'amount' => $amount->amount];
        } else {
            return ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->tolluserLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;

    }

    private function trip_details_payments($trip_id, $vechical_id, $trip_type, $params)
    {
        $tolls = json_decode($params['tollid']);
        $total = 0;
        foreach ($tolls as $key => $value) {
            $date = date('Y-m-d', strtotime('first day of next month'));
            $user_id = $params['user_id'];
            $amount = 0;
            $monthlypass = UserMonthlyTolls::find()->where("toll_id =$value->toll_id AND user_id = $user_id AND vechical_id = '$vechical_id' AND ('$date' BETWEEN valid_from AND valid_till)")->one();

            if ($value->trip_type == 3 && empty($monthlypass)) {
                $arr = json_encode([['toll_id' => $value->toll_id]]);
                $monthly_params = ['user_id' => $user_id, 'vechical_id' => $vechical_id, 'valid_from' => date('Y-m-d'), 'transation_id' => $params['transation_id'], 'toll_id' => $arr, 'trip_id' => $trip_id, 'trip_details_id' => $trip_id . 3 . $key];
                $output = Yii::$app->myfunctions->create_monthly($monthly_params);
            }

            $tollcost = TollCosts::find()->where(['toll_id' => $value->toll_id, 'vechical_types_id' => $params['vechical_type_id']])->one();
            //print_r($tollcost['single_trip_cost']); exit;
            $amount = (!empty($tollcost->single_trip_cost)) ? $tollcost->single_trip_cost : $tollcost['single_trip_cost'];
            $total = $total + $amount;
            $this->trip_type_dependency($trip_id, 1, $vechical_id, $tollcost, $monthlypass, $key, $params, $value->toll_id, $amount, 1, 1);
            if ($trip_type == 2 && $value->trip_type == 2) {
                $amount = $tollcost->round_trip_cost - $tollcost->single_trip_cost;
                $total = $total + $amount;
                $this->trip_type_dependency($trip_id, $value->trip_type, $vechical_id, $tollcost, $monthlypass, $key, $params, $value->toll_id, $amount, 2, 2);
            } elseif ($trip_type == 2 && $value->trip_type == 1) { //enable to make round trip
                //$amount = $tollcost->single_trip_cost;
                //$total = $total + $amount;
                //$this->trip_type_dependency($trip_id, $value->trip_type, $vechical_id,$tollcost,$monthlypass, $key, $params, $value->toll_id,$amount, 3,1);
            } elseif ($trip_type == 2 && $value->trip_type == 3) {
                $amount = 0;
                $total = $total + $amount;
                $this->trip_type_dependency($trip_id, 2, $vechical_id, $tollcost, $monthlypass, $key, $params, $value->toll_id, $amount, 3, 1);
            }
        }
        return $total;
    }

    private function trip_type_dependency($trip_id, $trip_type, $vechical_id, $tollcost, $monthlypass, $key, $params, $toll_id, $amount, $kind, $kind_cost)
    {
        $this->CreateTripdetails(['created_on' => date('Y-m-d H:i:s'), 'trip_id' => $trip_id, 'trip_details_id' => $trip_id . $kind . $key, 'toll_id' => $toll_id, 'trip_type' => $trip_type, 'vechical_id' => $vechical_id, 'vechical_type' => $params['vechical_type']]);
        $this->CreateTrippayments(['created_on' => date('Y-m-d H:i:s'), 'trip_id' => $trip_id, 'trip_details_id' => $trip_id . $kind . $key, 'toll_cost_id' => (!empty($monthlypass->user_monthly_tolls_id) && $params['vechical_type'] == 1) ? $monthlypass->user_monthly_tolls_id : (!empty($tollcost->single_trip_cost)) ? $tollcost->toll_cost_id : $tollcost['toll_cost_id'], 'amount' => (!empty($monthlypass['user_monthly_tolls_id']) && $params['vechical_type'] == 1) ? 0 : $amount, 'status_payed' => 1, 'trip_cost_type' => (!empty($monthlypass->user_monthly_tolls_id) && $params['vechical_type'] == 1) ? 3 : $kind_cost, 'booth_id' => 0, 'toll_user_id' => 0]);
    }

    private function route_select($params)
    {
        $from = $params['destination1'] = $params['from_location'];
        $to = $params['destination2'] = $params['to_location'];
        $d1l = $params['destination_1_lat'] = $params['from_location_lat'];
        $d1lg = $params['destination_1_lng'] = $params['from_location_lng'];
        $d2l = $params['destination_2_lat'] = $params['to_location_lat'];
        $d2lg = $params['destination_2_lng'] = $params['to_location_lng'];
        $overview_polyline = $params['polyline_overview'];
        $user_id = $params['user_id'];
        if (!empty($from) && !empty($to) && !empty($d1l) && !empty($d2l) && !empty($d1lg) && !empty($d2lg)) {
            //$where = "(user_id =$user_id AND (((destination1 = '$from' OR destination1 = '$to') AND (destination2 = '$from' OR destination2 = '$to')) OR ((round(destination_1_lat,2) = round($d1l,2) OR round(destination_1_lat,2) = round($d2l,2)) AND (round(destination_2_lat,2) = round($d1l,2) OR round(destination_2_lat,2) = round($d2l,2)) AND (round(destination_1_lng,2) = round($d1lg,2) OR round(destination_1_lng,2) = round($d2lg,2)) AND (round(destination_2_lng,2) = round($d1lg,2) OR round(destination_2_lng,2) = round($d2lg,2))))) AND polyline_overview = '$overview_polyline'";
            $where = "(user_id =$user_id AND (((destination1 = '$from' OR destination1 = '$to') AND (destination2 = '$from' OR destination2 = '$to')) OR ((round(destination_1_lat,2) = round($d1l,2) OR round(destination_1_lat,2) = round($d2l,2)) AND (round(destination_2_lat,2) = round($d1l,2) OR round(destination_2_lat,2) = round($d2l,2)) AND (round(destination_1_lng,2) = round($d1lg,2) OR round(destination_1_lng,2) = round($d2lg,2)) AND (round(destination_2_lng,2) = round($d1lg,2) OR round(destination_2_lng,2) = round($d2lg,2)))))";
            $route = new Userroutes();
            $user_route = Userroutes::find()->where($where)->one();
            $data['route_points_type'] = 1;
            /*if(!empty($user_route) && $user_route->destinatiion1 == $from && $user_route->destinatiion2 == $to){
                $data['route_points_type'] = 1;
            }else{
                $data['route_points_type'] = 2;
            }*/

            $route->attributes = $params;
            $route->user_route_id = 'UR' . '_' . $from[0] . $to[0] . $params['user_id'] . "_" . Userroutes::find()->where(['user_id' => $user_id])->count();
            $route->created_on = date("Y-m-d H:i:s");
            if (empty($user_route->user_route_id)) {
                if ($route->save()) {
                    $data['route_id'] = $route->user_route_id;
                } else {
                    $this->succes = false;
                }
            } else {
                $data['route_id'] = $user_route->user_route_id;
                unset($params['trip_type']);
                unset($params['vechical_type']);
                unset($params['vechical_id']);
                unset($params['tollid']);
                unset($params['transation_id']);
                unset($params['route_type']);
                unset($params['from_location']);
                unset($params['to_location']);
                unset($params['from_location_lat']);
                unset($params['from_location_lng']);
                unset($params['to_location_lat']);
                unset($params['to_location_lng']);
                unset($params['timezone']);
                unset($params['vechical_type_id']);
                unset($params['vechical_type']);
                unset($params['registration_no']);
                Userroutes::updateAll($params, ['user_route_id' => $user_route->user_route_id]);
                if ($user_route->destination1 == $to) {
                    $data['route_points_type'] = 1;
                }
            }
            if ($this->succes) {
                return $data;
            }

        }
        $this->succes = false;
    }


    private function vechical_details($type, $params)
    {
        if ($type == 2) {
            $reg_no = preg_replace('/\s+/', '', $params['registration_no']);
            $where = ['registration_no' => $params['registration_no'], 'vechical_type_id' => $params['vechical_type_id'], 'user_id' => $params['user_id'], 'vechical_id' => 'UR_' . $reg_no . '_' . $params['vechical_type_id'] . '_' . $params['user_id']];
            $vechical_id = UnregisteredVechicals::find()->where($where)->one();
            if (!empty($params['registration_no']) && !empty($params['vechical_type_id']) && !empty($params['user_id'])) {
                if (empty($vechical_id)) {
                    $unregistered = new UnregisteredVechicals();
//                    $unregistered->attributes = ['registration_no'zxbj k => $params['registration_no'], 'vechical_type_id' => $params['vechical_type_id'], 'user_id' => $params['user_id'], 'created_on' => date("Y-m-d H:i:s"), 'vechical_id' => 'UR_' . $reg_no . '_' .$params['vechical_type_id'].'_' . $params['user_id']];
                    if ($unregistered->save())
                        $vechical_id = $unregistered;
                    else
                        $this->succes = false;
                }
                return $vechical_id;
            }
        } else {
            if (!empty($params['vechical_id'])) {
                $where = ['vechical_id' => $params['vechical_id']];
                $vechical_id = VechicalDetails::find()->where($where)->one();
                if ($vechical_id)
                    return $vechical_id = $vechical_id;
                else
                    $this->succes = false;
            } else {

                $this->succes = false;
            }
        }
    }

    private function CreateTripdetails($data)
    {
        $model1 = new Tripdetails();
        $model1->attributes = $data;
        $model1->save();
    }

    private function CreateTrippayments($data)
    {

        $model = new Payments();
        $model->attributes = $data;
        //print_r($model);exit;
        $model->save();

    }

    public function actionCreate1()
    {
        date_default_timezone_set('asia/kolkata');
        $date = strtotime(date('YmdHis'));
        $model = new Trips();
        $model->attributes = $params = Yii::$app->request->post();
        $from = Yii::$app->request->post('from_location');
        $to = Yii::$app->request->post('to_location');
        $model->trip_id = $date . $from[0] . $to[0] . Yii::$app->request->post('user_id');
        $model->trip_type = Yii::$app->request->post('trip_type');
        $model->created_on = date('Y-m-d H:i:s');
        $tolls = json_decode(Yii::$app->request->post('tollid'));
        if ($model->save()) {
            foreach ($tolls as $key => $value) {
                $this->Tripdetails(['created_on' => date('Y-m-d H:i:s'), 'trip_id' => $model->trip_id, 'trip_details_id' => $model->trip_id . 1 . $key, 'toll_id' => $value->toll_id, 'trip_type' => 1]);
                if ($model->trip_type == 2) {
                    $this->Tripdetails(['created_on' => date('Y-m-d H:i:s'), 'trip_id' => $model->trip_id, 'trip_details_id' => $model->trip_id . 2 . $key, 'toll_id' => $value->toll_id, 'trip_type' => 2]);
                }
            }
            $data = Tripdetails::find()->joinWith('toll')->where(['trip_id' => $model->trip_id])->all();
            foreach ($data as $key => $value) {
                $trip[$key]['trip_details_id'] = $value->trip_details_id;
                $trip[$key]['toll_name'] = $value->toll->toll_name;
                $trip[$key]['toll_id'] = $value->toll_id;
                $trip[$key]['trip_type'] = $value->trip_type;
                $trip[$key]['trip_id'] = $value->trip_id;
            }
            $output = ['Code' => 200, 'Info' => $trip];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionGeofence()
    {
        $params = Yii::$app->request->post();
        $trip_details_id = Yii::$app->request->post('trip_details_id');
        $user_id = Yii::$app->request->post('user_id');
        $model = Tripdetails::find()->joinWith(['tblTrips', 'tblPayments'])->select('*')->where(['tbl_trip.user_id' => $user_id, 'tbl_trip_details.trip_details_id' => $trip_details_id])->one();
        $get = Tripdetails::find()->select('date(updated_on) as updated')->where(['status' => 1, 'trip_id' => $model->trip_id, 'toll_id' => $model->toll_id])->one();
        if ($model) {
            $Booth = false;
            if (!empty($params['boothside_id'])) {
                $Booth = $this->BoothSelection($model->toll_id, Yii::$app->request->post('boothside_id'));
            }
            if ($model->vechical_type == 1) {
                $vechical_type = VechicalDetails::find()->where(['vechical_id' => $model->vechical_id])->one();
            } else {
                $vechical_type = UnregisteredVechicals::find()->where(['vechical_id' => $model->vechical_id])->one();
            }
            $toll_cost = TollCosts::find()->where(['toll_id' => $model->toll_id, 'vechical_types_id' => $vechical_type->vechical_type_id])->one();
            $output = ['Code' => 200, 'Message' => 'Status Updated Sucessfully', 'Info' => array('Booth' => ($Booth) ? "Booth " . $Booth['booth_unique_id'] : 'Please look for InTag booth', 'toll_cost' => $toll_cost->single_trip_cost)];

            if ($model->trip_type == 2 && !empty($get) && $get->updated != date('Y-m-d')) {
                $tollcosts = TollCosts::find()->where(['toll_id' => $model->toll_id, 'vechical_types_id' => $model->vechical_type_id])->one();
                $amount = $tollcosts->round_trip_cost - ($tollcosts->round_trip_cost - $tollcosts->single_trip_cost);
                $output = ['Code' => 200, 'Message' => "Date already crossed, need to pay $amount amount after journey"];
            }
            $user = User::find()->where(['user_id' => $user_id])->one();
            //if(isset($params['us']) && $params['us'] == 1){
            //User::updateAll(['user_id' => $user_id],['amount' => $user->amount - $toll_cost->single_trip_cost]);
            $toll_details = Tolls::find()->where(['toll_id' => $model->toll_id])->one();
            // }

            if (!empty($params['us']) && $params['us'] == 1) {
                if (empty($params['direction_id'])) {
                    $params['direction_id'] = 0;
                }
                $trip_details = Tripdetails::find()->where(['trip_details_id' => $trip_details_id])->one();
                Tripdetails::updateAll(['status' => 1, 'direction_id' => $params['direction_id'], 'updated_on' => date("Y-m-d H:i:s")], ['trip_details_id' => $trip_details_id]);
                Trips::updateAll(['travel_date' => date("Y-m-d H:i:s")], ['trip_id' => $trip_details->trip_id]);
            } else {
                Tripdetails::updateAll(['status' => 2, 'updated_on' => date("Y-m-d H:i:s"), 'boothside_id' => (!empty($params['boothside_id'])) ? $params['boothside_id'] : 0, 'assigned_booth_id' => ($Booth) ? $Booth['booth_unique_id'] : 0], ['trip_details_id' => $trip_details_id]);
            }
            $toll_point_type = 2;
            if (!empty($params['toll_point_type'])) {
                $toll_point_type = $params['toll_point_type'];
            }

            if ($toll_point_type == 1) {
                $currency_type = '$';

                Yii::$app->myfunctions->callAPI(Url::base() . '/2119/tr/rhis', ['trip_details_id' => $trip_details_id, 'user_id' => $user_id, 'date' => date('Y-m-d'), 'getstatus' => !empty($get) ? 1 : 0]);
                if (!empty($params['currency_type'])) {
                    $currency_type = $params['currency_type'];
                }
                //FOR US Start
                try {
                    $mail_body = "<html><head></head>
                               <body>
                               <div style='width:100%'>
                               <table width='100%'>
                               <thead>
                               <tr>
                                    <th ><img src='http://115.124.125.42/~happyweb/tollrdemo/images/tollr-logo.png' height='50px'></th>
                                    <th ></th>
                                    <th >Receipt</th>
                                </tr>
                               </thead>
                               </table>
                               </div>

                                Receipt From Toll <br>
                                $toll_details->toll_name <br>
                                --------------------------- <br>
                                Amount:        $toll_cost->single_trip_cost <br>
                                </body>
                                </html>";
                    $date_tr = date('Y-m-d H:i:s');
                    $mail_body = "<html>
<head>
</head>
<body>
<table class='body-wrap' style='margin: 0; border-collapse: collapse;
width: 100%; margin-top: 3%; padding: 0 10%' align='center'>
           <tbody><tr>
             <td class='main-container' style='margin: 0 auto; padding:
40px 40px 20px 40px; display: block; max-width: 600px; clear: both;
width: 80%; -webkit-border-top-left-radius: 3px;
-webkit-border-top-right-radius: 3px; -moz-border-radius-topleft: 3px;
-moz-border-radius-topright: 3px; border-top-left-radius: 3px;
border-top-right-radius: 3px; -webkit-box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.1); -moz-box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.1);
box-shadow: 0px 0px 8px 0px rgba(0,0,0,0.1)' align='center'
bgcolor='#FFFFFF'>
               <table width='100%'>
                 <tbody><tr>
                   <td align='center'>
                     <div class='content-modules-template'>
                       <div class='content-modules-template--drop'>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>
<div class='module-wrapper'>

   <table class='content-module' border='0' cellpadding='0'
cellspacing='0' width='100%'>
     <tbody><tr>
       <td colspan='3' align='center' valign='middle' width='80%'>
         <a
href='http://email.receiptful.com/c/eJxtkLtqxDAQRb_G6lbMaKRZqVARCAtpA4G0th5rg9Yyfvx_5LAkBAJTXG5x5nCDj1PKLD4v7ymkadnzUS4fW1rfojecQGnNDmCIDBkAVY9sRPRWO7Ri8gqQgcChRtYkEa6kJDs03GlYf4gy1IcYveuRMFuKEWJm05sUje2DBafDVSGJ4sd9Xzp66dStHVqSVp1UiaRbbbG1ey2lX5aWSr1Xucz3jm6_vzp6PcUJcDCaMaRTnLMGsGL12zFPZWhy9XGUY5vmXPcUxm_BPxs843OG_2hfKyJb0g'
class='image-link' style='outline: 0; border: none; text-decoration:
none' target='_blank' rel='noreferrer'>
         <img class='template-img template-logo'
src='https://d14sm6a273ku3g.cloudfront.net/8fa54874eaf9c7df8a88d13f985d734b.png'
style='height: auto' height='53' width='110'>
         </a>
       </td>
     </tr>
   </tbody></table>
</div>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody>
   </table>
<table class='content-module' border='0' cellpadding='0' cellspacing='0'
width='100%'>
   <tbody><tr>
     <td colspan='3' align='center' valign='middle' width='80%'>
       <div class='template-h2 template-message font-montserrat'
style='margin: 0; padding: 0; font-family: \"Helvetica Neue\", Helvetica,
Arial, sans-serif; font-size: 18px; line-height: 1.4; color: #32373b;
font-weight: 300; letter-spacing: 0px; word-break: break-word'>
         <div><p>Thanks for using Tollr. <br></p><p>Billed to
$user->firstname <br></p></div>
       </div>
     </td>
   </tr>
</tbody></table>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>
<div class='module-wrapper receipt'>

   <table class='content-module' border='0' cellpadding='0'
cellspacing='0' width='100%'>
     <tbody><tr>
       <td>
         <!-- node type 8 -->

         <table class='primary-header' style='border-collapse: collapse;
background-color: orange; vertical-align: middle; width: 100%;
-webkit-border-top-left-radius: 3px; -webkit-border-top-right-radius:
3px; -moz-border-radius-topleft: 3px; -moz-border-radius-topright: 3px;
border-top-left-radius: 3px; border-top-right-radius: 3px; min-width:
280px'>
           <tbody><tr>
             <td align='center' height='100' valign='middle'>
               <h2 class='font-montserrat' style='margin: 0; padding: 0;
font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; font-size:
22px; line-height: 1.3; color: #ffffff; font-weight: 500;
letter-spacing: 0px!important; word-break: break-word'><span>Toll
Receipt Date</span></h2>
               <h3 class='font-montserrat' style='margin: 0; padding: 0;
font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; font-size:
13px; line-height: 1.3; color: #ffffff; font-weight: 500;
text-transform: uppercase; letter-spacing: 0.063em; word-break:
break-word'>  <!--March 9, 2016, 7:45 PM--> $date_tr </h3>
             </td>
           </tr>
         </tbody></table>

         <!-- node type 8 -->

         <table class='secondary-header' style='border-collapse:
collapse; width: 100%; background-color: darkorange; min-width: 280px'>
         <tbody><tr>
           <td align='center' height='50' valign='middle'>
             <p class='font-montserrat' style='margin: 0; padding: 0;
font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; font-size:
14px; line-height: 1.6; font-weight: 400; color: #ffffff;
letter-spacing: 0px!important; word-break: break-word'><span>Invoice
Number:</span> c_1e23d</p>
           </td>
         </tr>
         </tbody></table>

         <!-- node type 8 -->

         <table class='cost-table' style='border-collapse: collapse;
width: 100%; background-color: #f8f8f8;
-webkit-border-bottom-right-radius: 3px;
-webkit-border-bottom-left-radius: 3px; -moz-border-radius-bottomright:
3px; -moz-border-radius-bottomleft: 3px; border-bottom-right-radius:
3px; border-bottom-left-radius: 3px; min-width: 280px'>
           <tbody><tr>
             <td align='center'>

               <table style='margin: 5px auto 20px auto; border-collapse:
collapse; width: 95%'>
                 <thead>
                   <tr>


                       <th style='margin: 0; padding: 5px 0; font-family:
'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px;
line-height: 14px; text-align: left; width: 15%; color: #32373b;
text-transform: uppercase; font-weight: 800; letter-spacing: 0;
border-bottom: 3px solid #d3d3d3; height: 30px; word-break: break-word'>

                       </th>

                       <th style='margin: 0; padding: 5px 0; width: 2%;
border-bottom: 3px solid #d3d3d3; height: 30px'></th>

                       <th style='margin: 0; padding: 5px 0; font-family:
'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px;
line-height: 14px; text-align: left; width: 40%; color: #32373b;
text-transform: uppercase; font-weight: 800; letter-spacing: 0;
border-bottom: 3px solid #d3d3d3; height: 30px; word-break: break-word'>
                         <span>Fare breakdown</span>
                       </th>

                       <th style='margin: 0; padding: 5px 0; font-family:
'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px;
line-height: 14px; text-align: right; width: 20%; color: #32373b;
text-transform: uppercase; font-weight: 800; letter-spacing: 0;
border-bottom: 3px solid #d3d3d3; height: 30px; word-break: break-word'>
                         <span></span>
                       </th>

                       <th style='margin: 0; padding: 5px 0; font-family:
'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px;
line-height: 14px; text-align: right; width: 20%; color: #32373b;
text-transform: uppercase; font-weight: 800; letter-spacing: 0;
border-bottom: 3px solid #d3d3d3; height: 30px; word-break: break-word'>
                         <span></span>
                       </th>


                   </tr>
                 </thead>

                 <tbody class='table-body'>

                    <tr style='border-bottom: 1px solid #e4e7e9'>


                         <td style='margin: 0; padding: 12px 0; width:
15%; height: 75px; text-align: center' height='75'>

                           <table width='100%'>
                             <tbody><tr>
                               <td class='image' style='padding: 5px'
align='center' height='75'  width='75'>

                               </td>
                             </tr>
                           </tbody></table>

                         </td>

                         <td style='margin: 0; padding: 12px 0; width:
2%' height='75'></td>

                         <td class='description font-montserrat'
style='margin: 0; padding: 12px 0; font-family:,
Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: left; width: 40%; word-break: break-word' height='75'>
                           Toll Cost<br>
                           <span style='font-size:
12px'><span>Quantity</span>: 1</span>
                         </td>

                         <td class='unit-price font-montserrat'
style='margin: 0; padding: 12px 0; font-family: ,
Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: right; width: 20%; word-break: break-word' height='75'>
                           $ $toll_cost->single_trip_cost
                         </td>

                         <td class='amount font-montserrat'
style='margin: 0; padding: 12px 0; font-family:
Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: right; width: 20%; word-break: break-word' height='75'>
                           $ $toll_cost->single_trip_cost                       </td>
                     </tr>
                 </tbody>

                 <tfoot>
                     <tr>
                         <td colspan='2' style='margin: 0; padding: 10px
0; font-family: Helvetica, Arial, sans-serif;
font-size: 12px; line-height: 16px; text-align: left; color: #32373b;
text-transform: uppercase; font-weight: 800!important; border-bottom:
2px solid #e4e7e9; word-break: break-word' height='20'></td>
                       <td colspan='2' class='total-title' style='margin:
0; padding: 10px 0; font-family: Helvetica, Arial,
sans-serif; font-size: 12px; line-height: 16px; text-align: left; color:
#32373b; text-transform: uppercase; font-weight: 800!important;
border-bottom: 2px solid #e4e7e9; word-break: break-word'
height='20'>Discount</td>
                        <td class='total' style='margin: 0; padding: 10px
0; font-family: Helvetica, Arial, sans-serif;
font-size: 15px; line-height: 16px; text-align: right; color: #32373b; font-weight: 400; border-bottom: 2px solid #e4e7e9; word-break:break-word' height='20'>-$0.00</td>
                     </tr>
                   <tr>
                         <td colspan='2' style='margin: 0; padding: 20px
0; font-family: Helvetica, Arial, sans-serif;
font-size: 12px; line-height: 16px; text-align: left; color: #32373b;
text-transform: uppercase; font-weight: 800!important; border-bottom:
3px solid #d3d3d3; word-break: break-word' height='30'></td>
                       <td class='full-total' style='margin: 0; padding:
20px 0; font-family: Helvetica, Arial, sans-serif;
font-size: 16px; line-height: 16px; text-align: left; color: #32373b;
text-transform: uppercase; font-weight: 800!important; border-bottom:
3px solid #d3d3d3; word-break: break-word'
height='30'><span>Total</span></td>
                       <td class='full-total' style='margin: 0; padding:
5px 0; font-family: Helvetica, Arial, sans-serif;
font-size: 11px; line-height: 13px; text-align: right; width: 20%;
color: #32373b; letter-spacing: 0; word-break: break-word'
height='30'></td>
                         <td class='full-total' style='margin: 0;
padding: 20px 0; font-family: Helvetica, Arial,
sans-serif; font-size: 16px; line-height: 16px; text-align: right;
color: #32373b; font-weight: 800; border-bottom: 3px solid #d3d3d3;
word-break: break-word' height='30'>$  $toll_cost->single_trip_cost</td>

                   </tr>
                   <tr class='print-hide'>
                     <td colspan='4' class='paid-with' style='margin: 0;
padding: 0; line-height: 16px; height: 15px' height='15'
valign='middle'>
                     </td>
                   </tr>

                   <tr>
                         <td colspan='5' class='paid-with' style='margin:
0; padding: 0; font-family: Helvetica, Arial,
sans-serif; font-size: 12px; line-height: 16px; color: #32373b;
text-align: center; font-weight: 400; height: 30px; word-break:
break-word' height='30' valign='middle'>


                     </td>
                   </tr>

                 </tfoot>
               </table>
             </td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>


</div>
   <table style='width: 100%' class='print-hide'>
     <tbody><tr>
       <td align='center'>
         <table style='border-collapse: collapse; width: 100%'>
           <tbody><tr>
             <td colspan='3' height='10' width='100%'></td>
           </tr>
         </tbody></table>
       </td>
     </tr>
   </tbody></table>
                      </div>
                     </div>
                   </td>
                 </tr>
               </tbody></table>
             </td>
           </tr>
         </tbody></table>

</body>
</html>";
                    Yii::$app->mailer->compose('layouts/html', ['content' => $mail_body])
                        ->setFrom(['donotreply@tollr.world' => 'Tollr Receipt'])
                        ->setTo($user->user_email)
                        ->setSubject('Receipt From Tollr')
                        ->send();
                } catch (\Swift_TransportException $exception) {

                }
                //For US end
            }


            $sms_message = "Approaching Toll Plaza";
            $sms_message = urlencode($sms_message);
            $sms_mobile = $user->mobile_number;
            $url = "https://alerts.sinfini.com/api/v3/index.php?method=sms&api_key=A02cd64a770e413dc100f473d732ca680&to=$sms_mobile&sender=TOLLRI&message=$sms_message&format=json&custom=1&flash=0";

            $curl = curl_init();
            // Set some options -
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
            ));
            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);


        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog(Yii::$app->request->post('user_id'), [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionGeofence1()
    {
        $params = Yii::$app->request->post();
        $trip_details_id = Yii::$app->request->post('trip_details_id');
        $user_id = Yii::$app->request->post('user_id');
        $model = Tripdetails::find()->joinWith('tblTrips')->where(['tbl_trip.user_id' => $user_id, 'trip_details_id' => $trip_details_id])->all();
        if ($model) {
            Tripdetails::updateAll(['status' => 2, 'updated_on' => date("Y-m-d H:i:s")], ['trip_details_id' => $trip_details_id]);
            $output = ['Code' => 200, 'Message' => 'Status Updated Sucessfully'];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog(Yii::$app->request->post('user_id'), [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;

    }

    public function actionIndex()
    {
        //return $this->POSTrip(1,1,1,1,1);
        //$this->HitPOSserver(66,['trip_details_id' => 1,'trip_id' =>1 ,'created_on'=> '2016-05-12','user_id'=>1,'vehical_id'=> 1,'vehical_type'=> 1,'assigned_booth_id'=> 1,'firstname'=> 2,'lastname'=> 1,'user_email'=> 2,'mobile_number'=> 1,'address1' => 1,'address2' => 3 ,'zipcode'=> 4,'registration_no'=> 5,'vehical_type_id'=> 2,'vehical_drive_type'=> 3],'tripdetails');
        return $this->HitPOSserver(66, ['trip_details_id' => '1463053442_UR_3A6_5_6110'], 'geofence');

        $route = $this->routeDetails(1, "UR_Bc1_0");
        return $route->destination_1_lat;
        return $this->BoothSelection(4, 1);
        return $this->fromdetection(4, 12.9716, 77.59457);
        echo "I am in Index";
        exit;
        return new ActiveDataProvider([
            'query' => \api\models\Trips::find(),
        ]);
    }


    public function actionUserlogpath()
    {
        $params = Yii::$app->request->post();
        $model = new Userlogpath();
        krsort($params);
        $model->attributes = $params;
        if ($model->save()) {
            $output = ["Code" => 200, "Message" => "Successfully added"];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->directionuserLog($params['user_id'], $params);
        return $output;
    }

    public function actionCanceltrip($id)
    {
        $trip = Trips::findByTripid($id);
        $params = [];
        if (!empty($trip->user_id)) {
            $params['user_id'] = $trip->user_id;
        } else {
            $params['user_id'] = 0;
        }

        $result = Tripdetails::find()->select('trip_details_id')->where("trip_id='" . $id . "' AND status <>'4'")->all();

        $wallet = WalletTransactions::find()->where("trip_id='" . $id . "' AND transation_type = 40")->one();
        //$params = ['user_id' => $trip->user_id, 'transation_id' => $trip->transation_id, 'transation_type' => 11, 'created_on' => date('Y-m-d H:i:s'),'trip_id'=>$id];

        if (!empty($result)) {
            $result = Tripdetails::find()->select('trip_details_id')->where("trip_id='" . $id . "' AND status ='0'")->all();
            //echo count($result); exit;
            if (empty($result)) {
                $output = ["Code" => 484, "Error" => 'Crossed all tolls'];
            } else {
                $result = ArrayHelper::getColumn($result, 'trip_details_id');
                $amount = Payments::find()->select("sum(amount) as amount")->where(['IN', 'trip_details_id', $result])->one();
                $params = ['user_id' => $trip->user_id, 'transation_id' => $wallet->transation_id, 'amount' => $amount->amount, 'transation_type' => 11, 'created_on' => date('Y-m-d H:i:s'), 'trip_id' => $id];
                $model = new WalletTransactions();
                $model->attributes = $params;
                //$model->save();
                //print_r($model); exit;
                $error_message = true;
                if ($model->save()) {
                    //$result = Tripdetails::deleteAll(['IN', 'trip_details_id', $result]);
                    //Payments::deleteAll(['IN', 'trip_id', $result]);
//                if ($tdcount == 0) {
//                    Trips::deleteAll('trip_id = :tid', [':tid' => $id]);
//                }
                    if ($result) {
                        $user = User::findIdentity($trip->user_id);
                        $tot_amount = $amount->amount + $user->refund_amount;
                        $ac_amount = $user->amount;
                        User::updateAll(['refund_amount' => $tot_amount, 'amount' => $ac_amount], ['user_id' => $trip->user_id]);
                        Tripdetails::updateAll(['status' => 4], ['trip_id' => $id, 'status' => 0]);
                        $count = Tripdetails::find()->where(['trip_id' => $id])->count();
                        if ($count == 0) {
                            Trips::deleteAll(['trip_id' => $id]);
                        }
                        $error_message = false;

                        $output = ["Code" => 200, "Info" => 'Trip Cancelled Successfully'];
                    }
                    if ($error_message)
                        $output = ['Code' => 493, "Error" => "Something went wrong try again"];
                } else {
                    $output = ['Code' => 499, 'Error' => 'Insufficient data'];
                }
            }

        } else {

            $output = ['Code' => 485, 'Error' => 'you have already canceled trip'];
        }

        Yii::$app->alog->userLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }


    public function actionCanceltrip1($id)
    {
        $trip = Trips::findByTripid($id);
        if ($trip->vechical_type == 1) {
            $vechical_type = VechicalDetails::find()->where(['user_id' => $trip->user_id])->one();
            $vechical = $vechical_type->vechical_type_id;
        } else {
            $vechical_type = UnregisteredVechicals::find()->where(['user_id' => $trip->user_id])->one();
            $vechical = $vechical_type->vechical_type_id;
        }
        if ($trip) {
            $trip_details = Tripdetails::find()->where(['trip_id' => $id])->andWhere(['!=', 'status', '1'])->all();
            if ($trip_details) {
                $amount = 0;
                foreach ($trip_details as $key => $value) {
                    $toll_costs = TollCosts::find()->where(['toll_id' => $value->toll_id, 'vechical_types_id' => $vechical])->all();
                    ($value->trip_type == 1) ? ($amount = $amount + $toll_costs[0]->single_trip_cost) : $amount = $amount + ($toll_costs[0]->round_trip_cost - $toll_costs[0]->single_trip_cost);
                }
            }
            $params = ['user_id' => $trip->user_id, 'transation_id' => $id, 'amount' => $amount, 'transation_type' => 11, 'created_on' => date('Y-m-d H:i:s')];
            $model = new WalletTransactions();
            $model->attributes = $params;
            $error_message = true;
            if ($model->save()) {
                $tdcount = Tripdetails::find()->where(['status' => 1, 'trip_id' => $id])->count();
                foreach ($trip_details as $key => $value) {
                    Payments::deleteAll('trip_id = :tid AND status_payed != :st', [':tid' => $value->trip_details_id, ':st' => 2]);
                }
                $result = Tripdetails::deleteAll('trip_id = :tid AND status != :st', [':tid' => $id, ':st' => 1]);
                if ($tdcount == 0) {
                    Trips::deleteAll('trip_id = :tid', [':tid' => $id]);
                }
                if ($result) {
                    $user = User::findIdentity($trip->user_id);
                    $tot_amount = $amount + $user->refund_amount;
                    $ac_amount = $user->amount - $amount;
                    User::updateAll(['refund_amount' => $tot_amount, 'amount' => $ac_amount], ['user_id' => $trip->user_id]);
                    $error_message = false;
                    $output = ["Code" => 200, "Info" => 'Trip Cancelled Successfully'];
                }
            }
            if ($error_message)
                $output = ['Code' => 493, "Error" => "Something went wrong try again"];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }


    public function actionRepeattrip()
    {
        $params = Yii::$app->request->post();
        date_default_timezone_set('asia/kolkata');
        $date = strtotime(date('YmdHis'));
        $trip = Trips::find()->where(['trip_id' => $params['trip_id']])->one();
        $model = new Trips();
        $model->attributes = $params;
        $model->route_points_type = 1;
        $model->route_type = $trip->route_type;
        $model->route_id = $trip->route_id;
        $model->fav_type = $trip->fav_type;
        $model->trip_type = $trip->trip_type;
        $model->trip_id = $date . '_' . $trip->route_id . "_" . $trip->route_type . "_" . $params['user_id'];
        $vec_det = $this->vechical_details($params['vechical_type'], $params);
        $vechical_id = $vec_det->vechical_id;
        $params['vechical_type_id'] = $vec_det->vechical_type_id;
        $model->created_on = date('Y-m-d H:i:s');
        $tolls = Tripdetails::find()->select(['toll_id'])->where(['trip_id' => $params['trip_id']])->all();
        //$params['tollid'] = json_encode($tolls);
        //$model->save();
        //return $model;
        if ($this->succes && $model->save()) {
            $params['route_id'] = $trip->route_id;
            $route = $this->routeDetails($trip->route_type, $trip->route_id);
            $params['from_location_lat'] = $route->destination_1_lat;
            $params['from_location_lng'] = $route->destination_1_lng;
            $trip = $this->walet_payment_details($model->trip_id, $vechical_id, $model->trip_type, $params);
            $output = ['Code' => 200, 'Info' => $trip];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->tolluserLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function routeDetails($route_type, $route_id)
    {
        if ($route_type == 1) {
            $route = Userroutes::find()->where(['user_route_id' => $route_id])->one();
        } else {
            $route = UserCommonroutes::find()->where(['common_route_id' => $route_id])->one();
        }
        return $route;
    }

    private function walet_payment_details($trip_id, $vechical_id, $trip_type, $params)
    {
        $total = $this->trip_details_payments($trip_id, $vechical_id, $trip_type, $params);
        $wallet = new WalletTransactions();
        $wallet->attributes = ['user_id' => $params['user_id'], 'transation_id' => $params['transation_id'], 'amount' => $total, 'created_on' => date('Y-m-d H:i:s'), 'transation_type' => 40, 'trip_id' => $trip_id];
        if ($wallet->save()) {
            $amount = User::findone(['user_id' => $params['user_id']]);
            $amount = $amount->amount - $total;
            User::updateAll(['amount' => $amount], ['user_id' => $params['user_id']]);
        }
        $data = Tripdetails::find()->joinWith('toll')->where(['trip_id' => $trip_id])->all();
        foreach ($data as $key => $value) {
            $trip[$key]['trip_details_id'] = $value->trip_details_id;
            $trip[$key]['toll_name'] = $value->toll->toll_name;
            $trip[$key]['toll_unique_number'] = $value->toll->toll_unique_number;
            $trip[$key]['toll_location'] = $value->toll->toll_location;
            $trip[$key]['toll_lat'] = $value->toll->toll_lat;
            $trip[$key]['toll_lng'] = $value->toll->toll_lng;
            $trip[$key]['toll_id'] = $value->toll_id;
            $trip[$key]['trip_type'] = $value->trip_type;
            $trip[$key]['trip_id'] = $value->trip_id;
            $trip[$key]['vechical_id'] = $value->vechical_id;
            $trip[$key]['route_id'] = $params['route_id'];
            $trip[$key]['reference_points'] = ReferencePoints::find()->select(['lat', 'lng', 'direction_id'])->where(['toll_id' => $value->toll_id])->all();
            $both_sides = $this->fromdetection($value->toll_id, $params['from_location_lat'], $params['from_location_lng']);
            $trip[$key]['boothside_id'] = (!empty($both_sides)) ? $both_sides->boothside_id : 0;
            $trip[$key]['boothside_from'] = (!empty($both_sides)) ? $both_sides->boothside_from : 0;
            $trip[$key]['boothside_lat'] = (!empty($both_sides)) ? $both_sides->lat : 0;
            $trip[$key]['boothside_lng'] = (!empty($both_sides)) ? $both_sides->lng : 0;
        }

        return $trip;
    }

    private function fromdetection($toll_id, $source_lat, $source_lng)
    {
        return TollBoothside::find()->select("*, ( 3959 * acos( cos( radians($source_lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($source_lng)) + sin(radians($source_lat)) * sin( radians(lat)))) AS distance")->where(['toll_id' => $toll_id])->orderBy(['distance' => 'ASC'])->one();
    }

    private function BoothSelection($toll_id, $booth_side)
    {
        $command = Yii::$app->db->createCommand("CALL BoothSelection($toll_id, $booth_side)");
        return $results = $command->queryOne();
    }

    public function actionHistory($id) //Trip History
    {

        $command = Yii::$app->db->createCommand("CALL TripHistory($id)");
        $results = (array)$command->queryAll();
        foreach ($results as $key => $value) {
            $results[$key]['polyline_overview'] = json_decode($value['polyline_overview']);
            $results[$key]['path'] = json_decode($value['path']);
            $results[$key]['trip_waypoints'] = json_decode($value['trip_waypoints']);
            $command = Yii::$app->db->createCommand("CALL TripHistoryDetails({$id},'{$value['trip_id']}')");
            $array_details[$value['trip_id']] = (array)$command->queryAll();
        }
        //print_r($results); exit;
        //$results = Trips::find()->where(['user_ida' => $id])->limit(20)->orderBy(['created_on' => SORT_DESC])->groupBy('route_id')->all();

        if ($results) {
            $output = ["Code" => 200, "Info" => $results, "trip_details" => $array_details];
        } else {
            $output = ['Code' => 204, 'Info' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Url::canonical(), date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionFavourite($id) //All favority trips
    {
        $command = Yii::$app->db->createCommand("CALL TripFav($id)");
        $results = (array)$command->queryAll();
        foreach ($results as $key => $value) {
            $results[$key]['polyline_overview'] = json_decode($value['polyline_overview']);
            $results[$key]['path'] = json_decode($value['path']);
            $results[$key]['trip_waypoints'] = json_decode($value['trip_waypoints']);
            $command = Yii::$app->db->createCommand("CALL TripHistoryDetails({$id},'{$value['trip_id']}')");
            $array_details[$value['trip_id']] = (array)$command->queryAll();
        }
        if ($results) {
            $output = ["Code" => 200, "Info" => $results, "trip_details" => $array_details];
        } else {
            $output = ['Code' => 204, 'Info' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Url::canonical(), date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionFavupdate()
    {
        $params = Yii::$app->request->post();
        $results = UserRouteSelection::find()->where(['user_id' => $params['user_id'], 'route_id' => $params['route_id']])->one();
        if (isset($params['fav_type'])) {
            $array_update['fav_type'] = $params['fav_type'];
        }
        if (!empty($params['nick_name'])) {
            $array_update['nick_name'] = $params['nick_name'];
        }
        if (!empty($params['trip_id'])) {
            $array_update['trip_id'] = $params['trip_id'];
            $results = UserRouteSelection::find()->where(['user_id' => $params['user_id'], 'route_id' => $params['route_id'], 'trip_id' => $params['trip_id']])->one();
        }
        if (isset($array_update) && !empty($params['user_id']) && !empty($params['route_id'])) {

            if ($results) {
                $results = UserRouteSelection::updateAll($array_update, ['user_id' => $params['user_id'], 'route_id' => $params['route_id'], 'trip_id' => $params['trip_id']]);
                //Trips::updateAll(['fav_type' => 1], ['user_id' => $params['user_id'], 'trip_id' => $params['trip_id']])
            } else {
                $trip = Trips::find()->where(['user_id' => $params['user_id'], 'route_id' => $params['route_id']])->one();
                $params['route_type'] = $trip->route_type;
                $model = new UserRouteSelection();
                $model->attributes = $params;
                $results = $model->save();
            }
            if (!empty($results)) {
                $user_id = $params['user_id'];
                $command = Yii::$app->db->createCommand("CALL TripHistory($user_id)");
                $results = $command->queryAll();
                $output = ["Code" => 200, "Info" => $results];
            } else {
                $output = ['Code' => 478, 'Error' => 'Something wrong with in data'];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }

        Yii::$app->alog->userLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params['user_id']), json_encode($output)]);
        return $output;
    }

    public function actionCommonroutes()
    {
        $params = Yii::$app->request->post();


        $result = UserCommonroutes::find()->where(['LIKE', 'destination1', $params['location']])->orwhere(['LIKE', 'destination2', $params['location']])->all();
        //krsort($params);
        if ($result) {
            $output = ["Code" => 200, "Info" => $result];
        } else {
            $output = ['Code' => 204, 'Error' => 'No Content'];
        }
        //return $model;
        //Yii::$app->alog->directionuserLog($params['user_id'], $params);
        return $output;
    }


    public function actionReturntrip()
    {
        $params = Yii::$app->request->post();
        $date = date('Y-m-d');

        if (empty($params) || empty($params['user_promt']) || empty($params['user_id']) || empty($params['trip_id']) || empty($params['time_zone'])) {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        } else {
            $trip = Trips::find()->where(['user_id' => $params['user_id'], 'trip_id' => $params['trip_id'], 'travel_date' => $date])->one();
            $user_id = $params['user_id'];
            if ($trip) {
                $tripdetails = Tripdetails::find()->select('*')->where("trip_id='" . $params['trip_id'] . "' AND trip_type !='2'")->all();
                if ($tripdetails) {
                    if ($params['user_promt'] == 1) {//to know the status
                        $output = ["Code" => 200, "Info" => $tripdetails];
                    } else {//to create return trip
                        $error_msg = false;
                        $i = 1;
                        $total = 0;
                        foreach ($tripdetails as $tripdet) {
                            $params['vechical_type'] = $tripdet->vechical_type;
                            $tollcost = TollCosts::findOne(['toll_id' => $tripdet->toll_id, 'vechical_types_id' => VechicalDetails::findOne(['vechical_id' => $tripdet->vechical_id])->vechical_type_id]);
                            $monthlypass = UserMonthlyTolls::find()->where("toll_id =$tripdet->toll_id AND user_id = $user_id AND vechical_id = '$tripdet->vechical_id' AND ('$date' BETWEEN valid_from AND valid_till)")->one();
                            $amount = $tollcost->round_trip_cost - $tollcost->single_trip_cost;
                            $this->trip_type_dependency($tripdet->trip_id, $tripdet->trip_type, $tripdet->vechical_id, $tollcost, $monthlypass, $i, $params, $tripdet->toll_id, $amount, 2, 2);

                            $i++;
                        }
                        $wmodel = new WalletTransactions();
                        $wmodel->attributes = ['user_id' => $params['user_id'], 'transation_id' => $tripdet->trip_id . '2' . $i, 'amount' => $amount, 'trip_type' => '2', 'transation_type' => 40, 'created_on' => date('Y-m-d H:i:s')];
                        if (!$wmodel->save())
                            $error_msg = true;

                        if ($error_msg) {
                            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
                        } else {
                            $output = ["Code" => 200, "Info" => 'Success'];
                        }
                    }
                } else {
                    $output = ['Code' => 204, 'Error' => 'No single trips available'];
                }
            } else {
                $output = ['Code' => 204, 'Error' => 'No trips available'];
            }
        }
        return $output;
    }

    public function actionPendingtrip()
    {
        $params = Yii::$app->request->post();
        $command = Yii::$app->db->createCommand("CALL PendingTrip({$params['user_id']})");
        $results = (array)$command->queryAll();
        foreach ($results as $key => $value) {
            $results[$key]['trip_waypoints'] = json_decode($value['trip_waypoints']);
            $results[$key]['reference_points'] = ReferencePoints::find()->select(['lat', 'lng', 'direction_id'])->where(['toll_id' => $value['toll_id']])->all();
        }

        if (!empty($results)) {
            $user_route = Userroutes::find()->where(['user_route_id' => $results[0]['route_id']])->one();
            $user_route['path'] = json_decode($user_route->path);
            $user_route['polyline_overview'] = json_decode($user_route->polyline_overview);
            $output = ["Code" => 200, "Info" => ['trip_details' => $results, 'route_details' => $user_route]];
        } else {
            $output = ["Code" => 204, "Error" => 'No Pending Trips'];
        }
        return $output;
    }

    public function actionTriphistorydetails()
    {
        $params = Yii::$app->request->post();
        $command = Yii::$app->db->createCommand("CALL TripHistoryDetails({$params['user_id']},'{$params['trip_id']}')");
        $results = (array)$command->queryAll();

        if (!empty($results)) {
            $output = ["Code" => 200, "Info" => ['trip_details' => $results]];
        } else {
            $output = ["Code" => 204, "Error" => 'No Details for this trip'];
        }
        return $output;
    }

    private function POSTrip($id, $user_id, $vehical_id, $vechical_type, $trip_type)
    {
        $id = "1463053442_UR_3A6_5_6";
        $user_id = 6;
        $vehical_id = "6VID_AP36EM9339";
        $vechical_type = 1;
        $trip_type = 1;
        $trips = Tripdetails::find()->where(['trip_id' => $id])->all();
        $user = User::find()->where(['user_id' => $user_id])->one();
        $userdetails = UserDetails::find()->where(['user_id' => $user_id])->one();
        if ($vechical_type == 1) {
            $vehical = VechicalDetails::find()->where(['vechical_id' => $vehical_id, 'user_id' => $user_id])->one();
        } else {
            $vehical = UnregisteredVechicals::find()->where(['vechical_id' => $vehical_id, 'user_id' => $user_id])->one();
        }
        //print_r(data('Y-m-d H:s:i'));
        //return ['trip_details_id' => $trips->trip_details_id,'trip_id' =>$trips->trip_id ,'created_on'=> date('Y-m-d H:s:i'),'user_id'=>$user_id,'vehical_id'=> $trips->vechical_id,'vehical_type'=> $trips->vehical_type,'assigned_booth_id'=> $trips->assigned_booth_id,'firstname'=> $user->first_name,'lastname'=> $user->last_name];
        foreach ($trips as $key => $value) {
            $this->HitPOSserver($value->toll_id, ['trip_details_id' => $value->trip_details_id, 'trip_id' => $value->trip_id, 'created_on' => date('Y-m-d H:s:i'), 'user_id' => $user_id, 'vehical_id' => $value->vechical_id, 'vehical_type' => $value->vechical_type, 'assigned_booth_id' => $value->assigned_booth_id, 'firstname' => $user->firstname, 'lastname' => $user->lastname, 'user_email' => $user->user_email, 'mobile_number' => $user->mobile_number, 'address1' => $userdetails->address1, 'address2' => $userdetails->address2, 'zipcode' => $userdetails->zipcode, 'registration_no' => $vehical->registration_no, 'vehical_type_id' => $vehical->vechical_type_id, 'vehical_drive_type' => $vechical_type, 'trip_type' => $trip_type], 'tripdetails');
        }
        return;
        //$this->HitPOSserver($trips->toll_id,['trip_details_id' => $trips->trip_details_id,'trip_id' =>$trips->trip_id ,'created_on'=> data('Y-m-d H:s:i'),'user_id'=>1,'vehical_id'=> 1,'vehical_type'=> 1,'assigned_booth_id'=> 1,'firstname'=> 2,'lastname'=> 1,'user_email'=> 2,'mobile_number'=> 1,'address1' => 1,'address2' => 3 ,'zipcode'=> 4,'registration_no'=> 5,'vehical_type_id'=> 2,'vehical_drive_type'=> 3],'tripdetails');

        print_R($trips);
        exit;
    }

    public function HitPOSserver($id, $params, $lastmethod)
    {
        $toll = Tolls::find()->where(['toll_id' => $id])->one();
        if (!empty($toll) && !empty($toll->allowed_ip)) {
            echo $URL = 'http://' . $toll->allowed_ip . '/POS_SERVER/' . $lastmethod;
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_URL, $URL);
            //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            $resulta = curl_exec($ch);
            if (curl_errno($ch)) {
                print curl_error($ch);
            } else {
                curl_close($ch);
            }
            return $resulta;
        }
        return;
    }

    public function actionHistorydetails()
    {
        $params = Yii::$app->request->post();
        $model = Tripdetails::find()->joinWith(['tblTrips', 'tollCost', 'Vechicaldetails'])->where(['tbl_trip.user_id' => $params['user_id'], 'trip_details_id' => $params['trip_details_id']])->all();
        if ($model) {
            $tollcost = $this->getcost($params, $model);
            $gethdetails = HistoryOfPayments::find()->where(['toll_id' => $model[0]['toll_id'], 'vehical_type' => $model[0]['tollCost']['vechical_types_id'], 'date' => $params['date']])->one();
            $gethddetails = HistoryDateWithvechicaltypes::find()->where(['toll_id' => $model[0]['toll_id'], 'date' => $params['date']])->one();
            $amountfeild = "amount_" . $model[0]['Vechicaldetails']['vechical_type_id'];
            $counterfeild = "counter_" . $model[0]['Vechicaldetails']['vechical_type_id'];
            $countersinglefeild = "counter_single_" . $model[0]['Vechicaldetails']['vechical_type_id'];
            $counterdoublefeild = "counter_double_" . $model[0]['Vechicaldetails']['vechical_type_id'];
            $countermonthlyfeild = "counter_monthly_" . $model[0]['Vechicaldetails']['vechical_type_id'];
            if (!empty($gethddetails) && !empty($gethdetails)) {
                $hdamount = $gethddetails[$amountfeild] + $tollcost['tollcost'];
                $hdcounter = $gethddetails[$counterfeild] + 1;
                $hdscounter = $gethddetails[$countersinglefeild];
                $hddcounter = $gethddetails[$counterdoublefeild];
                $hdmcounter = $gethddetails[$countermonthlyfeild];
                if ($tollcost['counter'] == 1) {
                    $hdscounter = $gethddetails[$countersinglefeild] + 1;
                }
                if ($tollcost['counter'] == 2) {
                    $hddcounter = $gethddetails[$counterdoublefeild] + 1;
                }
                if ($tollcost['counter'] == 3) {
                    $hdmcounter = $gethddetails[$countermonthlyfeild] + 1;
                }
                $amount = $gethdetails['amount'] + $tollcost['tollcost'];
                $counter = $gethdetails['counter'] + 1;
                HistoryDateWithvechicaltypes::updateAll([$amountfeild => $amount, $counterfeild => $counter, $countersinglefeild => $hdscounter, $counterdoublefeild => $hddcounter, $countermonthlyfeild => $hdmcounter], ['history_date_withvechicaltypes_id' => $gethddetails['history_date_withvechicaltypes_id'], 'toll_id' => $model[0]['toll_id'], 'date' => $params['date']]);
                HistoryOfPayments::updateAll(['amount' => $amount, 'counter' => $counter], ['history_payment_id' => $gethdetails['history_payment_id'], 'toll_id' => $model[0]['toll_id'], 'vehical_type' => $model[0]['Vechicaldetails']['vechical_type_id'], 'date' => $params['date']]);
                $output = ["Code" => 200, "Message" => "Successfully Updated"];
                return $output;
            } else {
                $historypaymentid = "HS" . strtotime(date('Y-m-d')) . "_" . $model[0]['toll_id'] . "_" . $model[0]['tollCost']['vechical_types_id'];
                $historypaymentdatewiseid = "HSD" . strtotime(date('Y-m-d')) . "_" . $model[0]['toll_id'];
                $hdscounter = 0;
                $hddcounter = 0;
                $hdmcounter = 0;
                if ($tollcost['counter'] == 1) {
                    $hdscounter = 1;
                }
                if ($tollcost['counter'] == 2) {
                    $hddcounter = 1;
                }
                if ($tollcost['counter'] == 3) {
                    $hdmcounter = 1;
                }
                $model2 = new HistoryDateWithvechicaltypes();
                $model2->attributes = ['history_date_withvechicaltypes_id' => $historypaymentdatewiseid, 'toll_id' => $model[0]['toll_id'], $amountfeild => $tollcost['tollcost'], $counterfeild => 1, $countersinglefeild => $hdscounter, $counterdoublefeild => $hddcounter, $countermonthlyfeild => $hdmcounter, 'date' => $params['date']];
                $model2->$amountfeild = $tollcost['tollcost'];
                $model2->$counterfeild = 1;
                $model2->$countersinglefeild = $hdscounter;
                $model2->$counterdoublefeild = $hddcounter;
                $model2->$countermonthlyfeild = $hdmcounter;
                $model1 = new HistoryOfPayments();
                $model1->attributes = ['history_payment_id' => $historypaymentid, 'amount' => $tollcost['tollcost'], 'toll_id' => $model[0]['toll_id'], 'vehical_type' => $model[0]['Vechicaldetails']['vechical_type_id'], 'date' => $params['date'], 'counter' => 1];
                if ($model2->save() && $model1->save()) {
                    $output = ["Code" => 200, "Message" => "Successfully added"];
                } else {
                    $output = ["Code" => 400, "Message" => "Try Again"];
                }
                return $output;
            }
        }
    }

    private function getcost($param, $data)
    {
        $params = ['user_id' => $param['user_id'], 'toll_id' => $data[0]['toll_id'], 'vechical_id' => $data[0]['vechical_id'], 'trip_type' => $data[0]['tblTrips'][0]['trip_type']];
        $where = "user_id = '" . $param['user_id'] . "' and toll_id = '" . $params['toll_id'] . "' and vechical_id = '" . $params['vechical_id'] . "' and date('Y-m-d') between valid_from and valid_till";
        $get = UserMonthlyTolls::find()->where($where)->one();
        if (!empty($get)) {
            $tollcost = 0;
            $rparam = ['tollcost' => $tollcost, 'counter' => 3];
        } else {
            $tollcost = $data[0]['tollCost']['single_trip_cost'];
            $rparam = ['tollcost' => $tollcost, 'counter' => 1];
            if ($params['trip_type'] == 2) {
                $tollcost = $data[0]['tollcost']['round_trip_cost'];
                $rparam = ['tollcost' => $tollcost, 'counter' => 2];
                if ($param['getstatus'] == 1) {
                    $tollcost = 0;
                    $rparam = ['tollcost' => $tollcost, 'counter' => 2];
                }
            }
        }
        return $rparam;
    }

    public function actionTripreport()
    {
        $model = Tripdetails::find()->joinWith(['tblTrips', 'tollCost', 'vehicaldetails'])->where(['tbl_trip_details.status' => 1])->all();
        foreach ($model as $key => $value) {
            $modelr = ['toll_id' => $model[$key]['toll_id'], 'vechical_id' => $model[$key]['vechical_id'], 'trip_type' => $model[$key]['tblTrips'][0]['trip_type'], 'single_trip_cost' => $model[$key]['tollCost']['single_trip_cost'], 'double_trip_cost' => $model[$key]['tollCost']['round_trip_cost'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on'])), 'getstatus' => $model[$key]['status']];
            $tollcost = $this->getcosts($modelr);
            $gethdetails = HistoryOfPayments::find()->where(['toll_id' => $model[$key]['toll_id'], 'vehical_type' => $model[$key]['tollCost']['vechical_types_id'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on']))])->one();
            $gethddetails = HistoryDateWithvechicaltypes::find()->where(['toll_id' => $model[$key]['toll_id'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on']))])->one();
            if (!empty($model[$key]['vehicaldetails']['vechical_type_id'])) {
                $amountfeild = "amount_" . $model[$key]['vehicaldetails']['vechical_type_id'];
                $counterfeild = "counter_" . $model[$key]['vehicaldetails']['vechical_type_id'];
                $countersinglefeild = "counter_single_" . $model[$key]['vehicaldetails']['vechical_type_id'];
                $counterdoublefeild = "counter_double_" . $model[$key]['vehicaldetails']['vechical_type_id'];
                $countermonthlyfeild = "counter_monthly_" . $model[$key]['vehicaldetails']['vechical_type_id'];
                if (!empty($gethddetails) && !empty($gethdetails)) {
                    $hdamount = $gethddetails[$amountfeild] + $tollcost['tollcost'];
                    $hdcounter = $gethddetails[$counterfeild] + 1;
                    $hdscounter = $gethddetails[$countersinglefeild];
                    $hddcounter = $gethddetails[$counterdoublefeild];
                    $hdmcounter = $gethddetails[$countermonthlyfeild];
                    if ($tollcost['counter'] == 1) {
                        $hdscounter = $gethddetails[$countersinglefeild] + 1;
                    }
                    if ($tollcost['counter'] == 2) {
                        $hddcounter = $gethddetails[$counterdoublefeild] + 1;
                    }
                    if ($tollcost['counter'] == 3) {
                        $hdmcounter = $gethddetails[$countermonthlyfeild] + 1;
                    }
                    $amount = $gethdetails['amount'] + $tollcost['tollcost'];
                    $counter = $gethdetails['counter'] + 1;
                    HistoryDateWithvechicaltypes::updateAll([$amountfeild => $amount, $counterfeild => $counter, $countersinglefeild => $hdscounter, $counterdoublefeild => $hddcounter, $countermonthlyfeild => $hdmcounter], ['history_date_withvechicaltypes_id' => $gethddetails['history_date_withvechicaltypes_id'], 'toll_id' => $model[$key]['toll_id'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on']))]);
                    HistoryOfPayments::updateAll(['amount' => $amount, 'counter' => $counter], ['history_payment_id' => $gethdetails['history_payment_id'], 'toll_id' => $model[$key]['toll_id'], 'vehical_type' => $model[$key]['tollCost']['vechical_types_id'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on']))]);
                    $output = ["Code" => 200, "Message" => "Successfully Updated"];
                } else {
                    $historypaymentid = "HS" . strtotime(date('Y-m-d', strtotime($model[$key]['updated_on']))) . "_" . $model[$key]['toll_id'] . "_" . $model[$key]['tollCost']['vechical_types_id'];
                    $historypaymentdatewiseid = "HSD" . strtotime(date('Y-m-d', strtotime($model[$key]['updated_on']))) . "_" . $model[$key]['toll_id'] . "_" . $model[$key]['tollCost']['vechical_types_id'];
                    $hdscounter = 0;
                    $hddcounter = 0;
                    $hdmcounter = 0;
                    if ($tollcost['counter'] == 1) {
                        $hdscounter = 1;
                    }
                    if ($tollcost['counter'] == 2) {
                        $hddcounter = 1;
                    }
                    if ($tollcost['counter'] == 3) {
                        $hdmcounter = 1;
                    }
                    $model2 = new HistoryDateWithvechicaltypes();
                    $model2->attributes = ['history_date_withvechicaltypes_id' => $historypaymentdatewiseid, 'toll_id' => $model[$key]['toll_id'], $amountfeild => $tollcost['tollcost'], $counterfeild => 1, $countersinglefeild => $hdscounter, $counterdoublefeild => $hddcounter, $countermonthlyfeild => $hdmcounter, 'date' => date('Y-m-d', strtotime($model[$key]['updated_on']))];
                    $model2->$amountfeild = $tollcost['tollcost'];
                    $model2->$counterfeild = 1;
                    $model2->$countersinglefeild = $hdscounter;
                    $model2->$counterdoublefeild = $hddcounter;
                    $model2->$countermonthlyfeild = $hdmcounter;
                    $model1 = new HistoryOfPayments();
                    $model1->attributes = ['history_payment_id' => $historypaymentid, 'amount' => $tollcost['tollcost'], 'toll_id' => $model[$key]['toll_id'], 'vehical_type' => $model[$key]['tollCost']['vechical_types_id'], 'date' => date('Y-m-d', strtotime($model[$key]['updated_on'])), 'counter' => 1];
                    if ($model2->save() && $model1->save()) {
                        $output = ["Code" => 200, "Message" => "Successfully added"];
                    } else {
                        $output = ["Code" => 400, "Message" => "Try Again"];
                    }
                }
            }

        }
        return $output;
    }

    private function getcosts($data)
    {
        $where = "toll_id = '" . $data['toll_id'] . "' and vechical_id = '" . $data['vechical_id'] . "' and '" . $data['date'] . "' between valid_from and valid_till";
        $get = UserMonthlyTolls::find()->where($where)->one();
        if (!empty($get)) {
            $tollcost = 0;
            $rparam = ['tollcost' => $tollcost, 'counter' => 3];
        } else {
            $tollcost = $data['single_trip_cost'];
            $rparam = ['tollcost' => $tollcost, 'counter' => 1];
            if ($data['trip_type'] == 2) {
                $tollcost = $data['double_trip_cost'];
                $rparam = ['tollcost' => $tollcost, 'counter' => 2];
                if ($data['getstatus'] == 1) {
                    $tollcost = 0;
                    $rparam = ['tollcost' => $tollcost, 'counter' => 2];
                }
            }
        }
        return $rparam;
    }


}
