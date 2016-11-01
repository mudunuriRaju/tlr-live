<?php
namespace api\controllers;

use api\models\TollBooths;
use api\models\TollBoothside;
use api\models\Tolls;
use api\models\UserMonthlyTolls;
use Yii;
use yii\db\Exception;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use api\models\Trips;
use api\models\Tripdetails;
use api\models\VechicalDetails;
use api\models\TollCosts;
use api\models\User;
use api\models\UserDetails;
use api\models\WalletTransactions;
use api\models\TollUsers;
use yii\helpers\BaseStringHelper;
use yii\helpers\BaseJson;
use yii\helpers\Url;
use api\models\MasterVechicalTypes;
use api\models\UserTripTollExtraPayments;
use yii\swiftmailer;
use yii\mail;

/**
 * Site controller
 */
class PossController extends Controller
{

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
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
    }

    public function actionSignin()
    {
        //date_default_timezone_set('Asia/Calcutta');
        $date = date('Y-m-d H:i:s', strtotime(' +1 day'));
        $params = Yii::$app->request->post();
        if (!empty($params['username']) && !empty($params['password']) && !empty($params['toll_unique_number'])) {
            $user = TollUsers::findByUsername($params['username']);
            $toll = Tolls::find()->where(['toll_unique_number' => $params['toll_unique_number']])->one();

            if ($user && $user->validatePassword($params['password']) && !empty($toll)) {
                Tolls::updateAll(['allowed_ip' => $_SERVER['REMOTE_ADDR']], ['toll_id' => $toll->toll_id]);
                $toll_users = TollUsers::find()->where(['toll_id' => $toll->toll_id])->all();
                $toll = $toll->toArray();
                $hash_string = $toll['toll_id'] . sha1($toll['toll_unique_number']) . sha1($toll['toll_id']);
                $toll_details = ['Tollkey' => $toll['toll_unique_number'], 'server_hash' => hash('sha256', $hash_string), 'toll_id' => $toll['toll_id'], 'toll_name' => $toll['toll_name'], 'toll_stretch' => $toll['toll_stretch'], 'motorway_id' => $toll['motorway_id'], 'toll_location' => $toll['toll_location'], 'toll_km' => $toll['toll_km']];

                $output = ['Code' => 200, 'Info' => $toll_details, 'Toll_users' => (array)$toll_users, 'Toll_URL' => $_SERVER['REMOTE_ADDR']];
            } else {
                $output = ['Code' => 498, 'Error' => 'Incorrect username or toll unique number or password.'];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        $userid = 0;
        if (!empty($user->toll_user_id)) {
            $userid = $user->toll_user_id;
        }
        Yii::$app->alog->tolluserLog($userid, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionStatus()
    {
        $params = Yii::$app->request->post();

        $model = Tripdetails::find()->joinWith(['tblTrips', 'tblPayments'])->where(['tbl_trip_details.trip_details_id' => $params['trip_details_id'], 'tbl_trip.user_id' => $params['user_id']])->one();
        if (!empty($model)) {
            $date = date('Y-m-d');
            $user_id = $params['user_id'];
            $amount = User::findone(['user_id' => Yii::$app->request->post('user_id')]);
            $vechical_id = VechicalDetails::find()->where(['vechical_id' => $model->vechical_id])->one();
            //return $model;
            $tollcosts = TollCosts::find()->where(['toll_id' => $model->toll_id, 'vechical_types_id' => $vechical_id->vechical_type_id])->one();
            $monthly_pass = UserMonthlyTolls::find()->where("toll_id =$model->toll_id AND user_id = $user_id AND vechical_id = '$model->vechical_id' AND ('$date' BETWEEN valid_from AND valid_till)")->one();
            if ($monthly_pass) { //To check wether it monthly pass toll or not
                $amount = 0;
            } else {
                if ($model->trip_type == 2) { //For this applied for single or double
                    $getcreated = Tripdetails::findOne(['trip_id' => $model->trip_id, 'toll_id' => $model->toll_id, 'trip_type' => 1]);
                    $amount = $tollcosts->round_trip_cost - $tollcosts->single_trip_cost;
                    if (date('Y-m-d', strtotime($getcreated->updated_on)) != date('Y-m-d')) { //Check wether he is doing the trip on same day or not
                        $extra = new UserTripTollExtraPayments();
                        $extra->attributes = ['user_id' => $params['user_id'], 'trip_details_id' => $params['trip_details_id'], 'amount' => $tollcosts->round_trip_cost - $amount, 'type_payments' => 1, 'created_on' => date('Y-m-d H:i:s')];
                        $extra->save(); // Save extra amount which user have to pay
                    }
                    $amount = $amount->amount - $amount;
                } else {
                    $amount = $amount->amount - $tollcosts->single_trip_cost;
                }
            }
            if ($amount > 0) {
                User::updateAll(['amount' => $amount], ['user_id' => Yii::$app->request->post('user_id')]);
            }
            $user = User::find()->where(['user_id' => $user_id])->one();
            $mail_body = 'Receipt From Tollr';
            Yii::$app->mailer->compose('layouts/html', ['content' => $mail_body])
                ->setFrom(['donotreply@tollr.world' => 'Tollr Receipt'])
                ->setTo($user->user_email)
                ->setSubject('Receipt From Tollr')
                ->send();
            $sms_message = "Receipt From Tollr just crossed toll";
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


            Tripdetails::updateAll(['status' => 1, 'updated_on' => date('Y-m-d H:i:s'), 'allowed_booth_id' => Yii::$app->request->post('allowed_booth_id'), 'toll_user_id' => Yii::$app->request->post('toll_user_id')], ['trip_details_id' => Yii::$app->request->post('trip_details_id'), 'toll_id' => $model->toll_id]);
            Trips::updateAll(['travel_date' => date('Y-m-d H:i:s')], ['trip_id' => $model->trip_id]);
            $output = $this->token_verfication($params, $model->toll_id);
            // $output = $this->vechical_list($model->toll_id);;
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->tolluserLog(Yii::$app->request->post('user_id'), [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionStatus1()
    {
        $params = Yii::$app->request->post();
        $model = Tripdetails::find()->joinWith('tblTrips')->where(['tbl_trip_details.trip_details_id' => Yii::$app->request->post('trip_details_id'), 'tbl_trip.user_id' => Yii::$app->request->post('user_id')])->one();
        if (!empty($model)) {
            $amount = User::findone(['user_id' => Yii::$app->request->post('user_id')]);
            $vechical_id = VechicalDetails::find()->where(['vechical_id' => $model->vechical_id])->one();
            $tollcosts = TollCosts::findAll(['toll_id' => $model->toll_id, 'vechical_types_id' => $vechical_id->vechical_type_id]);
            $getcreated = Tripdetails::findOne(['trip_id' => $model->trip_id, 'trip_type' => 1]);
            $wallet = new WalletTransactions();
            $wallet->user_id = Yii::$app->request->post('user_id');
            $wallet->transation_id = $model->trip_details_id;
            $wallet->created_on = date('Y-m-d H:i:s');
            $wallet->transation_type = 00;
            if ($model->trip_type == 2 && ($amount->amount > $tollcosts[0]->round_trip_cost - $tollcosts[0]->single_trip_cost) && date('Y-m-d', strtotime($getcreated->updated_on)) == date('Y-m-d')) {
                $amoun = $tollcosts[0]->round_trip_cost - $tollcosts[0]->single_trip_cost;
                $amount = $amount->amount - $amoun;
                $wallet->amount = $amoun;
            } elseif (($model->trip_type == 1 || $model->trip_type == 2) && $amount->amount > $tollcosts[0]->single_trip_cost) {
                $amount = $amount->amount - $tollcosts[0]->single_trip_cost;
                $wallet->amount = $tollcosts[0]->single_trip_cost;
            } else {
                $output = ['Code' => 492, 'Error' => 'Insuffecient funds'];
            }
            if ($wallet->save()) {
                User::updateAll(['amount' => $amount], ['user_id' => Yii::$app->request->post('user_id')]);
                Tripdetails::updateAll(['status' => 1, 'updated_on' => date('Y-m-d H:i:s')], ['trip_details_id' => Yii::$app->request->post('trip_details_id'), 'toll_id' => $model->toll_id]);
                $output = ['Code' => 200, 'Message' => 'Status Updated Sucessfully'];
            }

        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->tolluserLog(Yii::$app->request->post('user_id'), [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }


    public function actionView($id)
    {
        $params = Yii::$app->request->get();
        if (!empty($params['access_token']) && !empty($params['toll_user_id'])) {
            return $this->token_verfication($params, $id);
        } else {
            return ['Code' => 499, 'Error' => 'Insufficient data'];
        }
    }

    public function actionSearch($id)
    {
        $params = Yii::$app->request->post();
        //print_r($params);
        $user = TollUsers::find()->where(['toll_user_id' => $params['toll_user_id'], 'access_token' => $params['access_token']])->andWhere('expiry_date > NOW()')->one();
        if ($user) {
            return $this->vechical_list($id, $params);
        } else {
            return ['Code' => 480, 'Error' => 'Access token expired'];
        }
//
    }

    public function actionVserach()
    {
        $params = Yii::$app->request->post();
        $toll = Tolls::find()->where(['allowed_ip' => $_SERVER['REMOTE_ADDR'], 'toll_unique_number' => $params['Tollkey']])->one();
        if (!empty($toll) && $_POST['server_hash'] = hash('sha256', $toll->toll_id . sha1($toll->toll_unique_number) . sha1($toll->toll_id))) {
            return $vechicals = $this->vechical_list($toll->toll_id, $params);
            //return ['Code' => 499, 'Info' => $vechicals];
        } else {
            return ['Code' => 499, 'Info' => 'Autentication failed'];
        }

    }

    private function token_verfication($params, $id)
    {
        $user = TollUsers::find()->where(['toll_user_id' => $params['toll_user_id'], 'access_token' => $params['access_token']])->andWhere('expiry_date > NOW()')->one();
        if ($user) {
            return $this->vechical_list($id, $params);
        } else {
            return ['Code' => 480, 'Error' => 'Access token expired'];
        }
    }

    private function vechical_list($id, $params)
    {
        if (!empty($params['boothside_id'])) {
            $where = ['status' => 2, 'toll_id' => $id, 'date(tbl_trip_details.updated_on)' => date('Y-m-d'), 'boothside_id' => $params['boothside_id']];
            $where = ['status' => 2, 'toll_id' => $id, 'date(tbl_trip_details.updated_on)' => date('Y-m-d')];
        } else {
            $where = ['status' => 2, 'toll_id' => $id, 'date(tbl_trip_details.updated_on)' => date('Y-m-d')];
        }
        $data = Tolls::find()->where(['toll_id' => $id, 'toll_status' => 10])->one();
        //return $params;
        if (!empty($params['registration_number'])) {
            $command = Yii::$app->db->createCommand("CALL VechicalApproched($id,'{$params['registration_number']}')");
            $trip = (array)$command->queryAll();
            if (empty($trip)) {

                $trip1 = VechicalDetails::find()->where(['registration_no' => $params['registration_number']])->all();
                //$trip['toll_details'] = $data;
                if (!empty($trip1)) {
                    //$trip1['toll_details'] = $data;
                    return ['Code' => 206, 'Info' => $trip1, 'Message' => 'Not created trip'];
                } else {
                    return ['Code' => 206, 'Info' => $trip, 'Message' => 'No registered with Tollr'];

                }
            } else {
                //$trip['toll_details'] = $data;
                return ['Code' => 200, 'Info' => $trip];
            }
            //print_r($model1);
        } else {
            $model = (array)Tripdetails::find()->joinWith('tblTrips')->select(['*'])->where($where)->all();
        }

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $trip[$key]['user_id'] = $value->tblTrips[0]->user_id;
                $trip[$key]['trip_details_id'] = $value->trip_details_id;
                $trip[$key]['trip_id'] = $value->trip_id;
                $trip[$key]['trip_type'] = $value->trip_type;
                $trip[$key]['updated_on'] = $value->updated_on;
                $trip[$key]['vechical_id'] = $value->vechical_id;
                $user_details = User::find()->select(["CONCAT(firstname,' ', lastname) as firstname"])->where(['user_id' => $value->tblTrips[0]->user_id])->one();
                $trip[$key]['user_name'] = $user_details->firstname;
                $vechical_id = VechicalDetails::find()->where(['vechical_id' => $value->vechical_id])->one();
                $trip[$key]['registration_no'] = $vechical_id->registration_no;
                $vechical_type = MasterVechicalTypes::findOne(['vechical_types_id' => $vechical_id->vechical_type_id]);
                $trip[$key]['vechical_type'] = $vechical_type->type;
            }
            //$trip['toll_details'] = $data;
            return ['Code' => 200, 'Info' => $trip];
        } else {
            //$trip['toll_details'] = $data;
            return ['Code' => 206, 'Info' => $trip, 'Message' => 'No Vehicals is approched'];
        }
    }


    public function actionBoothsides($id)
    {
        $booths = TollBoothside::find()->where(['toll_id' => $id])->all();
        if (!empty($booths)) {
            return ['Code' => 200, 'Info' => $booths];
        } else {
            return ['Code' => 206, 'Info' => $booths, 'Message' => 'No sides Assigned'];
        }
    }

    public function actionBooths($id)
    {
        $booths = TollBooths::find()->where(['booth_side' => $id, 'login_status' => 0, 'status' => 10, 'login_user_id' => 0])->all();
        if (!empty($booths)) {
            return ['Code' => 200, 'Info' => $booths];
        } else {
            return ['Code' => 206, 'Info' => $booths, 'Message' => 'No sides Assigned'];
        }
    }

    public function actionBoothsign()
    {
        $params = Yii::$app->request->post();

        //$booths = TollBooths::find()->where(['booth_side' => $id,'login_status' => 0, 'status' => 10, 'login_user_id' => 0])->all();
        $update = TollBooths::updateAll(['login_status' => 1, 'login_user_id' => $params['toll_user_id']], ['booth_id' => $params['booth_id']]);
        if ($update) {
            return ['Code' => 200, 'Info' => 'true'];
        } else {
            return ['Code' => 206, 'Info' => 'true', 'Message' => 'No sides Assigned'];
        }
    }

    public function actionIndex()
    {
        echo "I am in Index";
        exit;
        return new ActiveDataProvider([
            'query' => \api\models\Trips::find(),
        ]);
    }

    public function actionUpdtripdetails()
    {
        $params = Yii::$app->request->post();
        $trip_id = Tripdetails::find()->where(['trip_details_id' => $params['trip_details_id']])->one();
        try {
            Tripdetails::updateAll(['status' => 1, 'updated_on' => date('Y-m-d H:i:s'), 'allowed_booth_id' => $params['allowed_booth_id'], 'toll_user_id' => $params['toll_user_id']], ['trip_details_id' => $params['trip_details_id'], 'toll_id' => $trip_id->toll_id]);
            Trips::updateAll(['travel_date' => date('Y-m-d H:i:s')], ['trip_id' => $trip_id->trip_id]);
            Tripdetails::updateAll(['status' => 1, 'updated_on' => date('Y-m-d H:i:s'), 'allowed_booth_id' => $params['allowed_booth_id'], 'toll_user_id' => $params['toll_user_id']], ['trip_details_id' => $params['trip_details_id'], 'toll_id' => $trip_id->toll_id]);
            Trips::updateAll(['travel_date' => date('Y-m-d H:i:s')], ['trip_id' => $trip_id->trip_id]);
            $user_id = Trips::find()->where(['trip_id' => $trip_id->trip_id])->one();
            $user_details = User::find()->where(['user_id' => $user_id->user_id])->one();
            $tolls = Tripdetails::find()->where(['trip_id' => $trip_id->trip_id,])->andWhere(['<>', 'status', 1])->count();
            if ($tolls == 0) {
                $this->SendReciptMail($user_details->user_email, $trip_id->trip_id, $params, $user_details);
                return;
                return ['Code' => '200', 'Info' => 1, 'Message' => 'Sucessfully Updated'];
            } else {
                return;
                return ['Code' => '200', 'Info' => 2, 'Message' => 'Sucessfully Updated'];
            }
        } catch (Exception $error) {
            return ['Code' => '499', 'Error' => $error];
        }


    }

    private function SendReciptSMS($to, $trip_id, $params, $user)
    {

    }

    private function SendReciptMail($to, $trip_id, $params, $user)
    {
        $total_toll_cost = 0;
        $toll_list_body = "";
        $trip_details = Tripdetails::find()->where(['trip_id' => $trip_id])->all();
        foreach ($trip_details as $key => $value) {
            $toll_details = Tolls::find()->where(['toll_id' => $value->toll_id])->one();
            $toll_cost = TollCosts::find()->where(['toll_id' => $value->toll_id])->one();
            $total_toll_cost = $total_toll_cost + $toll_cost->single_trip_cost;
            $toll_list_body .= "<tbody class='table-body'>

                    <tr style='border-bottom: 1px solid #e4e7e9'>
                         <td style='margin: 0; padding: 12px 0; width:15%; height: 75px; text-align: center' height='75'>
                           <table width='100%'>
                             <tbody><tr>
                               <td class='image' style='padding: 5px' align='center' height='75'  width='75'>                               </td>
                             </tr>
                           </tbody>
                           </table>

                         </td>
                         <td style='margin: 0; padding: 12px 0; width:2%' height='75'></td>
                         <td class='description font-montserrat'style='margin: 0; padding: 12px 0; font-family:Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: left; width: 40%; word-break: break-word' height='75'>
                           Toll Cost<br>
                           <span style='font-size:12px'><span>$toll_details->toll_name</span></span>
                         </td>

                         <td class='unit-price font-montserrat' style='margin: 0; padding: 12px 0; font-family: Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: right; width: 20%; word-break: break-word' height='75'>
                           ₹ $toll_cost->single_trip_cost
                         </td>

                         <td class='amount font-montserrat' style='margin: 0; padding: 12px 0; font-family:Helvetica, Arial, sans-serif; font-size: 16px; line-height: 20px;
text-align: right; width: 20%; word-break: break-word' height='75'>
                           ₹ $toll_cost->single_trip_cost                       </td>
                     </tr>
                 </tbody>";
        }
        try {
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

                 $toll_list_body

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
word-break: break-word' height='30'>₹  $total_toll_cost</td>

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
        return;
    }


}
