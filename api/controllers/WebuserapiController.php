<?php

namespace api\controllers;

use api\models\TollBoothside;
use api\models\UnregisteredVechicals;
use api\models\Userroutes;
use api\models\UserRouteSelection;
use api\models\VechicalDetails;
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
class WebuserapiController extends Controller
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
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
    }

    public function actionIndex()
    {
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

    public function actionRepeattrip()
    {
        $params = Yii::$app->request->post();
        date_default_timezone_set('Asia/Calcutta');
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

    public function actionHistory($id) //Trip History
    {
        $params = Yii::$app->request->post();
        if (!empty($params['from_date'])) {
            $command = Yii::$app->db->createCommand("CALL TripHistoryList($id,'{$params['from_date']}','{$params['to_date']}')");
        } else {
            $command = Yii::$app->db->createCommand("CALL TripHistoryWeb($id)");
        }

        $results = (array)$command->queryAll();

        //print_r($results); exit;
        //$results = Trips::find()->where(['user_ida' => $id])->limit(20)->orderBy(['created_on' => SORT_DESC])->groupBy('route_id')->all();

        if ($results) {
            $output = ["Code" => 200, "Info" => $results];
        } else {
            $output = ['Code' => 204, 'Info' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Url::canonical(), date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionFavourite($id) //All favority trips
    {
        $params = Yii::$app->request->post();
        $command = Yii::$app->db->createCommand("CALL TripFav($id)");
        $results = (array)$command->queryAll();
        if ($results) {
            $output = ["Code" => 200, "Info" => $results];
        } else {
            $output = ['Code' => 204, 'Info' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Url::canonical(), date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionPendingtrip()
    {
        $params = Yii::$app->request->post();
        $command = Yii::$app->db->createCommand("CALL PendingTrip({$params['user_id']})");
        $results = (array)$command->queryAll();

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
        $amount = 0;
        foreach ($results as $val) {
            $amount = $amount + $val['amount'];
        }
        if (!empty($results)) {
            $output = ["Code" => 200, "Info" => $results, 'Amount' => $amount];
        } else {
            $output = ["Code" => 204, "Error" => 'No Details for this trip'];
        }
        return $output;
    }

    public function actionVehiclelist($id)
    {
        $params = Yii::$app->request->post();
        if (!empty($id) && is_numeric($id)) {
            if (!empty($params['type'])) {
                $output = ['Code' => 200, 'Info' => VechicalDetails::findAll(['user_id' => $id, 'vechical_type_id' => $params['type']])];
            } else {
                $output = ['Code' => 200, 'Info' => VechicalDetails::findAll(['user_id' => $id])];
            }

        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        return $output;

    }

    public function actionVechicaltypes()
    {
        return new ActiveDataProvider([
            'query' => \api\models\MasterVechicalTypes::find(),
        ]);
    }

    public function actionCreatevehic()
    {
        $params['user_id'] = 0;
        $params = Yii::$app->request->post();
        //print_r($_FILES);
        //exit;


        if (!empty($params) && !empty($params['registration_no']) && !empty($params['user_id']) && !empty($params['vechical_type_id']) && isset($params['use_type']) && !empty($params['vechical_nickname'])) {
            $params['registration_no'] = preg_replace('/\s+/', '', $params['registration_no']);
            $uid = User::findOne(['user_id' => $params['user_id']]);
            $model = new VechicalDetails();
            $model->setscenario('create');
            $model->attributes = $params;
            $model->created_on = date('Y-m-d H:i:s');
            $registration_no = preg_replace('/\s+/', '', $params['registration_no']);
            $vid = $params['user_id'] . "VID_" . $registration_no;
            $model->vechical_id = $vid;
            if (!empty($_FILES)) {
                $target_dir = Yii::$app->params['vechicalpicPath'] . $params['user_id'];
                $uploadOk = 1;
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                foreach ($_FILES as $key => $value) {
                    $target_file = basename($_FILES[$key]["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    if ($key == "vechical_RC_pic") {
                        $file_image = 'RC_' . $registration_no . ".$imageFileType";
                    } else {
                        $file_image = 'bumper_' . $registration_no . ".$imageFileType";
                    }
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF") {
                        $uploadOk = 0;
                        $error_msg = "Problem with uploaded file type";

                    }
                    if ($uploadOk == 0) {
                        $error_msg = "Problem with uploaded file type or Not attempted to upload file";

                    } else {
                        if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_dir . '/' . $file_image)) {
                            $model->$key = $file_image;
                        } else {
                            $error_msg = "Problem with uploaded file";
                        }
                    }
                }
            }

            if ($model->save()) {
                if (empty($error_msg)) {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $params['user_id']])->all(), 'Message' => 'file uploaded successfully'];
                } else {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $params['user_id']])->all(), 'Error' => $error_msg];
                }
            } else {
                if (empty($uid['user_id'])) {
                    $output = ['Code' => 496, 'Error' => 'Problem with User id'];
                } elseif ($model->vechical_id == $vid) {
                    $output = ['Code' => 492, 'Error' => 'You already registered with this registration number'];
                }

            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        Yii::$app->alog->uservechicallog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output), 'add']);
        return $output;

    }

    public function actionUpdatevehic($id)
    {
        //print_r(Yii::$app->request->post()); exit;
        $user_id = 0;
        $param = Yii::$app->request->post();
        $information = VechicalDetails::findOne(['vechical_id' => $id]);
        if (!empty($param) && !empty($param['vechical_nickname']) && !empty($information)) {
            if (!empty($_FILES)) {
                $target_dir = Yii::$app->params['vechicalpicPath'] . $information['user_id'];
                $uploadOk = 1;
                foreach ($_FILES as $key => $value) {
                    $target_file = basename($_FILES[$key]["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    if ($key == "vechical_RC_pic") {
                        $file_image = 'RC_' . $information['registration_no'] . ".$imageFileType";
                    } else {
                        $file_image = 'bumper_' . $information['registration_no'] . ".$imageFileType";
                    }
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF") {
                        $uploadOk = 0;
                        $error_msg = "Problem with uploaded file type";
                    }
                    if ($uploadOk == 0) {
                        $error_msg = "Problem with uploaded file type or Not attempted to upload file";
                    } else {
                        if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_dir . '/' . $file_image)) {
                            $param[$key] = $file_image;
                        } else {
                            $error_msg = "Problem with uploaded file";
                        }
                    }
                }
            } else {
                $error_msg = "You have not uploaded a file";
            }
            if (!empty($param['timezone'])) {
                unset($param['timezone']);
            }
            $vechical_deta = VechicalDetails::find()->where(['registration_no' => $param['registration_no'], 'user_id' => $param['user_id']])->one();
            if (empty($vechical_deta) || (!empty($vechical_deta) && $vechical_deta['vechical_id'] == $id)) {
                if (!empty($param['use_type']) && $param['use_type'] == 1) {
                    $use_type = ['use_type' => 2];

                    VechicalDetails::updateAll($use_type, ['user_id' => $param['user_id']]);
                }
                VechicalDetails::updateAll($param, ['vechical_id' => $id]);
                if (empty($error_msg)) {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $param['user_id']])->all(), 'Message' => 'file uploaded successfully'];
                } else {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $param['user_id']])->all(), 'Error' => $error_msg];
                }
            } else {
                $output = ['Code' => 481, 'Error' => 'You have already registered'];
            }

        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        if (!empty($information['user_id'])) {
            $user_id = $information['user_id'];
        }
        Yii::$app->alog->userLog($user_id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        Yii::$app->alog->uservechicallog($user_id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($param), json_encode($output), 'delete']);
        return $output;
    }

    public function actionDeletevehi()
    {
        $param['user_id'] = 0;
        $param = Yii::$app->request->post();
        if (!empty($param['user_id']) && !empty($param['vechical_id'])) {
            if (!empty($param['timezone'])) {
                unset($param['timezone']);
            }
            $model = VechicalDetails::find()->where($param)->one();
            $model->delete();
            //VechicalDetails::deleteAll($param);
            //VechicalDetails::deleteAll('user_id = :uid AND registration_no = :regno', [':uid' => $param['user_id'], ':regno' => $param['registration_no']]);
            $output = ['Code' => 200, 'Info' => VechicalDetails::findAll(['user_id' => $param['user_id']])];
        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        Yii::$app->alog->uservechicallog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($param), json_encode($output), 'delete']);
        return $output;
    }

    public function actionLogin()
    {
        //$model = new LoginForm();
        $params = Yii::$app->request->post();
        date_default_timezone_set('asia/kolkata');
        $date = date('Y-m-d H:i:s', strtotime(' +1 day'));
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        if (!empty($username) && !empty($password)) {
            $user = User::findByUsername($username);
            //$user = User::findOne(['user_email' => $username, 'password' => $password]);
            if ($user && $user->validatePassword($password)) {
                User::updateAll(['access_token' => hash('sha256', $user->user_email), 'expiry_date' => $date], ['user_id' => $user->user_id]);
                $user = User::find()->joinWith('language')->where(['user_id' => $user->user_id])->one();
                $user->language_id = $user->language[0]->laguage_name;
                $data['user_details'] = $user->getAttributes(['user_id', 'firstname', 'lastname', 'mobile_number', 'user_email', 'profile_pic', 'driving_licence', 'access_token', 'language_id', 'language', 'login_status', 'amount']);
                $data['user_trips'] = Trips::find()->where(['user_id' => $user->user_id])->count();
                $output = ['Code' => 200, 'Info' => $data];
            } else {
                $output = ['Code' => 498, 'Error' => 'Incorrect username or password.'];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        $userid = 0;
        if (!empty($user->user_id)) {
            $userid = $user->user_id;
        }
        Yii::$app->alog->userLog($userid, [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionMonthlyPass($id)
    {
        $params = Yii::$app->request->post();
        date_default_timezone_set('asia/kolkata');
        $where = "tbl_user_monthly_tolls.user_id = '" . $id . "' and  CURDATE() between valid_from and valid_till";
        $get = UserMonthlyTolls::find()->joinWith(['toll', 'vechicaldetails'])->where($where)->asArray()->all();
        foreach ($get as $key => $value) {
            $get[$key] = $value;
            $get[$key]['toll_name'] = $get[$key]['toll']['toll_name'];
            $get[$key]['registration_no'] = $get[$key]['vechicaldetails']['registration_no'];
            unset($get[$key]['toll']);
            unset($get[$key]['vechicaldetails']);
        }
        if (!empty($get)) {
            $output = ['Code' => 200, 'Info' => $get];
        } else {
            $output = ['Code' => 498, 'Error' => "User Don't Have Monthlypass"];
        }
        return $output;
    }

}
