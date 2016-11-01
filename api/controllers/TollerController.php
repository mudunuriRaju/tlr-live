<?php
namespace api\controllers;

use api\models\TollBooths;
use api\models\TollBoothside;
use api\models\Tolls;
use api\models\UserMonthlyTolls;
use Yii;
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
class TollerController extends Controller
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
            if (!empty($user->group_id)) {
                $toll = TollUsers::find()->where(['toll_id' => $toll->toll_id, 'group_id' => $user->group_id])->one();
            }
            if ($user && $user->validatePassword($params['password']) && !empty($toll)) {
                TollUsers::updateAll(['access_token' => hash('sha256', $user->toll_employee_id), 'expiry_date' => $date], ['toll_user_id' => $user->toll_user_id]);
                $user = TollUsers::findAll(['toll_employee_id' => $params['username']]);
                $output = ['Code' => 200, 'Info' => array('toll' => $toll, 'user' => $user[0])];
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

    public function actionTolldetails($id)
    {
        $toll = Tolls::find()->where(['toll_id' => $id])->one();
        if (!empty($toll)) {
            return ['Code' => 200, 'Info' => $toll];
        } else {
            return ['Code' => 499, 'Error' => 'Fail'];
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

    public function actionList($id)
    {
        $params = Yii::$app->request->post();
        return $this->vechical_list($id, $params);
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
        if (!empty($params['registration_number'])) {
            $command = Yii::$app->db->createCommand("CALL VechicalApproched($id,'{$params['registration_number']}')");
            $trip = (array)$command->queryAll();
            if (empty($trip)) {
                //$trip['toll_details'] = $data;
                return ['Code' => 206, 'Info' => $trip, 'Message' => 'No Vehicals is approched'];
            }
            //$trip['toll_details'] = $data;
            return ['Code' => 200, 'Info' => $trip];
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
                $trip[$key]['vechical_types_id'] = 2;
                if ($value->trip_type == 1) {
                    $trip[$key]['trip_image'] = 'tripp-type-one.png';
                } else {
                    $trip[$key]['trip_image'] = 'tripp-type-two.png';
                }

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
            $trip['toll_details'] = $data;
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

    public function actionVechicaltypes()
    {
        return new ActiveDataProvider([
            'query' => \api\models\MasterVechicalTypes::find(),
        ]);
    }

    public function actionSearchvehical()
    {
        $params = $params = Yii::$app->request->post();
        $vechcals = $this->vechical_list($params['toll_id'], $params);

        if ($vechcals['Code'] != 206) {
            return ['Code' => 200, 'Info' => $vechcals['Info'], 'trip_type' => 1];
        }
        $registration_no = $params['registration_number'];
        $vechical = VechicalDetails::find()->where("registration_no LIKE '%$registration_no%'")->one();
        if (!empty($vechical)) {
            return ['Code' => 200, 'Info' => $vechical, 'trip_type' => 2];
        }
        return ['Code' => 200, 'Info' => array(), 'trip_type' => 3];
    }

    public function actionAuthentic()
    {
        $params = Yii::$app->request->post();
        $user = TollUsers::find()->where(['access_token' => $params['access_token']])->one();
        if (!empty($params)) {
            return ['Code' => 200, 'Info' => $user];
        } else {
            return ['Code' => 499, 'Error' => 'Some'];
        }
    }


}
