<?php

namespace api\controllers;

use api\models\VechicalDetails;
use api\models\MonthlyTypes;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use api\models\Tolls;
use api\models\TollCosts;
use api\models\UserMonthlyTolls;
use api\models\Payments;
use api\models\WalletTransactions;
use yii\helpers\Url;
use yii\db\Query;
use yii\db\Command;
use yii\db\ActiveRecord;
use api\models\UnregisteredVechicals;
use GeoTools\RouteBoxer;
use GeoTools\LatLng;
use GeoTools\LatLngBounds;
use GeoTools\LatLngCollection;
use Geokit\Math;
use api\models\tollusers;


/**
 * Site controller
 */
class TollController extends Controller
{
    private $succes = true;
    private $toll_ids = [];

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
        #special rules for particular action
        $behaviors['actions'] = [
            'Sampleeb' => [
                #web-servers which you alllow cross-domain access
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ]
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];
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


    public function actionIndex()
    {
        $monthlycost = TollCosts::findOne(['toll_id' => 1, 'vechical_types_id' => VechicalDetails::findOne(['vechical_id' => '1231as'])->vechical_type_id]);
        return Yii::$app->myfunctions->sort_monthly_pass($monthlycost, 1, []);
        exit;
        $data = Tolls::findAll(['toll_status' => 10]);
        if ($data) {
            $output = ["Code" => 200, "Info" => $data];
        } else {
            $output = ['Code' => 204, 'Message' => 'No Content'];
        }

        return $output;
    }

    public function actionView($id)
    {
        $data = Tolls::findAll(['toll_status' => 10]);
        if ($data) {
            $output = ["Code" => 200, "Info" => $data];
        } else {
            $output = ['Code' => 204, 'Message' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionTollslist($id)
    {
        //$data = Tolls::findAll(['toll_status' => 10]);
        $data = Tolls::find()->where(['toll_status' => 10])->andWhere(['like', 'toll_location', $id])->all();
        if ($data) {
            $output = ["Code" => 200, "Info" => $data];
        } else {
            $output = ['Code' => 204, 'Message' => 'No Content'];
        }
        //Yii::$app->alog->userLog($id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    //Overview Polyline with LatLan(encode version) with radius algorithm
    public function actionCreate2()
    {
        //$data_array = Yii::$app->request->post('user_id');
        //return [Yii::$app->request->post('user_id')];
        //return json_decode(Yii::$app->request->post());
        $data_array = json_decode(Yii::$app->request->post('latlng'));
        $data_trip = Yii::$app->request->post('trip_type');
        $vechical_id = Yii::$app->request->post('vechical_id');
        $user_id = Yii::$app->request->post('user_id');
        $vechical_type = Yii::$app->request->post('vechical_type');
        $params = Yii::$app->request->post();
        //return [$user_id];
        if (!empty($data_array) && !empty($data_trip) && !empty($user_id) && !empty($vechical_type)) {
            $vec_det = $this->vechical_details($params['vechical_type'], $params);
            if ($this->succes) {
                $vechical_id = $vec_det->vechical_id;
                $vechical_type_id = $vec_det->vechical_type_id;

                //$data_array = json_decode(Yii::$app->request->post('latlng'));
                if ($data_trip == 2) {
                    $trip_cost = "{{tbl_TollCosts}}.round_trip_cost AS trip_cost";
                } else {
                    $trip_cost = "{{tbl_toll_costs}}.single_trip_cost As trip_cost";
                }
                $i = 0;
                //return $data_array;
                foreach ($data_array as $key => $value) {
                    $this->toll_ids = array();
                    $data = $this->decodePolylineToArray($value->overview_polyline, $trip_cost, $user_id, $vechical_id, $vechical_type_id);
                    $data_ar = (array)$data;
                    $cost = 0;
                    if (!empty($data_ar)) {
                        $places = array();
                        $map_info = array('title', 'latitude', 'longitude');
                        if (!empty($data_ar['tolls'])) {
                            foreach ($data_ar['tolls'] as $keys => $values) {
                                $places[] = $values;
                                $cost = (int)$cost + (int)$values['trip_cost'];
                            }
                        }
                    }
                    $data_return[$i]['route_id'] = $value->route_id;
                    $data_return[$i]['tolls'] = $places;
                    //$data_return[$i]['path'] = $data_ar['path'];
                    $data_return[$i]['total_cost'] = $cost;
                    $i++;
                }
                if (!empty($places)) {
                    $output = ['Code' => 200, 'Info' => $data_return];
                } else {
                    $output = ['Code' => 204, 'Message' => 'No Content'];
                }
            } else {
                $output = ['Code' => 499, 'Error' => 'Insufficient data'];
            }

        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        //$data = ['access' => $token];
        //$data['pos'] = Yii::$app->request->post('KK');
        if (empty($params['user_id'])) {
            $params['user_id'] = 0;
        }
        Yii::$app->alog->userLog($params['user_id'], [Url::canonical(), date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;

    }

    private function vechical_details($type, $params)
    {
        if ($type == 2) {
            $reg_no = preg_replace('/\s+/', '', $params['registration_no']);
            $where = ['registration_no' => $params['registration_no'], 'vechical_type_id' => $params['vechical_type_id'], 'user_id' => $params['user_id'], 'vechical_id' => 'UR_' . $reg_no . '_' . $params['vechical_type_id'] . '_' . $params['user_id']];
            $vechical_id = UnregisteredVechicals::find()->where($where)->one();
            //print_r($vechical_id); exit;
            if (!empty($params['registration_no']) && !empty($params['vechical_type_id']) && !empty($params['user_id'])) {

                if (empty($vechical_id)) {
                    $unregistered = new UnregisteredVechicals();
                    $unregistered->attributes = ['registration_no' => $params['registration_no'], 'vechical_type_id' => $params['vechical_type_id'], 'user_id' => $params['user_id'], 'created_on' => date("Y-m-d H:i:s"), 'vechical_id' => 'UR_' . $reg_no . '_' . $params['vechical_type_id'] . '_' . $params['user_id']];
                    if ($unregistered->save())
                        $vechical_id = $unregistered;
                    else
                        $this->succes = false;
                }
                return $vechical_id;
            }
        } else {
            if (!empty($params['vechical_id'])) {
                $where = ['vechical_id' => $params['vechical_id'], 'user_id' => $params['user_id']];
                //$where = ['vechical_id' => $params['vechical_id']];
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

    //Overview Polyline with LatLan(encode version) with boxes algorithm
    public function actionCreate1()
    {
        $params = Yii::$app->request->post();
        //return $params;
        //$params['latlng'] = addslashes(Yii::$app->request->post('latlng'));
        //$params['latlng'] = stripslashes(Yii::$app->request->post('latlng'));
//return $params;
        /*for($i = 1; $i <= 3; $i++){
            $row1 = "overview_polyline".$i;
            $latlng[$i-1]['overview_polyline'] = $params[$row1];
            $latlng[$i-1]['route_id'] = $i;
        }*/
        $data_array = json_decode($params['latlng']);
        $data_trip = Yii::$app->request->post('trip_type');
        $vechical_types_id = Yii::$app->request->post('vechical_type');
        $user_id = Yii::$app->request->post('user_id');
        if (!empty($data_array) && !empty($data_trip) && !empty($vechical_types_id) && !empty($user_id)) {
            $data_array = json_decode($params['latlng']);
            $vec_det = $this->vechical_details($params['vechical_type'], $params);
            if ($this->succes) {
                $vechical_id = $vec_det->vechical_id;
                $vechical_type_id = $vec_det->vechical_type_id;
                if ($data_trip == 2) {
                    $trip_cost = "{{tbl_TollCosts}}.round_trip_cost AS trip_cost";
                } else {
                    $trip_cost = "{{tbl_toll_costs}}.single_trip_cost As trip_cost";
                }
                $i = 0;

                foreach ($data_array as $key => $value) {
                    $places = array();
                    $points = $this->decodePolylineToArraynew($value->overview_polyline);

                    $collection = new LatLngCollection($points);
                    $boxer = new RouteBoxer();
                    //calculate boxes with 10km distance from the line between points
                    $boxes = $boxer->box($collection, $distance = 0.2);
//print_r($boxes);
                    $j = 0;
                    //exit;
                    $cost = 0;
                    $place = array();
                    foreach ($boxes as $row) {

                        // print_r($row->southWest->latitude); exit;
                        $query = new Query;
                        $query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
                            ->from('tbl_tolls')
                            ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
                            ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
                            ->andWhere(['=', 'toll_status', 10])
                            ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_type_id])
                            ->andWhere(['>', 'toll_lat', $row->southWest->latitude])
                            ->andWhere(['<', 'toll_lat', $row->northEast->latitude])
                            ->andWhere(['>', 'toll_lng', $row->southWest->longitude])
                            ->andWhere(['<', 'toll_lng', $row->northEast->longitude]);
                        $command = $query->createCommand();
                        $data = $command->queryAll();
                        $data_ar = (array)$data;

                        if (!empty($data_ar) && !empty($data_ar[0])) {
                            //                          print_r($row);
//print_r($data_ar);
                            $prev = 0;
                            $next = 0;
                            foreach ($data_ar as $keys => $values) {
                                //print_r($data_ar); exit;
                                $distance = Yii::$app->myfunctions->distance($points[0][0], $points[0][1], $values['latitude'], $values['longitude']);

                                if ($keys == 0) {
                                    $prev = $distance;
                                } else {
                                    $next = $distance;
                                }
                                //print_r($values);
                                if (!in_array($values['toll_id'], $places)) {
                                    $places[] = $values['toll_id'];
                                    $place[$j] = $values;
                                    $place[$j]['distance'] = $distance;

                                    $cost = (int)$cost + (int)$values['trip_cost'];
                                    $j++;
                                }
                            }
                            if ($next < $prev) {
                                //array_multisort($tolly[$key]['tolls']);
                            }


                        }

                    }
//exit;
                    $tolly[$key]['route_id'] = $value->route_id;
                    $tolly[$key]['tolls'] = $place;
                    $tolly[$key]['total_cost'] = $cost;


                }
                if (!empty($tolly)) {
                    $output = ['Code' => '200', 'Info' => $tolly];
                } else {
                    $output = ['Code' => '204', 'Message' => 'No Content'];
                }
            } else {
                $output = ['Code' => '499', 'Error' => 'Insufficient data'];
            }


        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }

        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
        //$data = ['access' => $token];
        //$data['pos'] = Yii::$app->request->post('KK');
    }

//Overview Polyline with LatLan(decode version) with boxes algorithm
    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        $data_array = json_decode($params['latlng']);
        $data_trip = Yii::$app->request->post('trip_type');
        $vechical_types_id = Yii::$app->request->post('vechical_type');
        $user_id = Yii::$app->request->post('user_id');
        if (!empty($data_array) && !empty($data_trip) && !empty($vechical_types_id) && !empty($user_id)) {
            $data_array = json_decode($params['latlng']);
            $vec_det = $this->vechical_details($params['vechical_type'], $params);
            if ($this->succes) {
                $vechical_id = $vec_det->vechical_id;
                $vechical_type_id = $vec_det->vechical_type_id;
                if ($data_trip == 2) {
                    $trip_cost = "{{tbl_TollCosts}}.round_trip_cost AS trip_cost";
                } else {
                    $trip_cost = "{{tbl_toll_costs}}.single_trip_cost As trip_cost";
                }
                $i = 0;

                foreach ($data_array as $key => $value) {
                    $places = array();
                    $poly_points = $value->overview_polyline;
                    $points = [];
                    foreach ($poly_points as $values) {
                        $points[] = [$values->lat, $values->lng];
                    }
                    $collection = new LatLngCollection($points);
                    $boxer = new RouteBoxer();
                    //calculate boxes with 10km distance from the line between points
                    $boxes = $boxer->box($collection, $distance = 1);
//print_r($boxes);
                    $j = 0;
                    //exit;
                    $cost = 0;
                    $place = array();
                    foreach ($boxes as $row) {

                        // print_r($row->southWest->latitude); exit;
                        $query = new Query;
                        $query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
                            //$query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
                            ->from('tbl_tolls')
                            ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
                            ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
                            ->andWhere(['=', 'toll_status', 10])
                            ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_type_id])
                            ->andWhere(['>', 'toll_lat', $row->southWest->latitude])
                            ->andWhere(['<', 'toll_lat', $row->northEast->latitude])
                            ->andWhere(['>', 'toll_lng', $row->southWest->longitude])
                            ->andWhere(['<', 'toll_lng', $row->northEast->longitude]);
                        $command = $query->createCommand();
                        $data = $command->queryAll();
                        $data_ar = (array)$data;

                        if (!empty($data_ar) && !empty($data_ar[0])) {
                            $prev = 0;
                            $next = 0;
                            foreach ($data_ar as $keys => $values) {
                                $distance = Yii::$app->myfunctions->distance($points[0][0], $points[0][1], $values['latitude'], $values['longitude']);

                                if ($keys == 0) {
                                    $prev = $distance;
                                } else {
                                    $next = $distance;
                                }
                                //print_r($values);
                                if (!in_array($values['toll_id'], $places)) {
                                    $places[] = $values['toll_id'];
                                    $place[$j] = $values;
                                    $place[$j]['distance'] = $distance;

                                    $cost = (int)$cost + (int)$values['trip_cost'];
                                    $j++;
                                }
                            }
                            if ($next < $prev) {
                                //array_multisort($tolly[$key]['tolls']);
                            }


                        }

                    }
//exit;
                    $tolly[$key]['route_id'] = $value->route_id;
                    $tolly[$key]['tolls'] = $place;
                    $tolly[$key]['total_cost'] = $cost;


                }
                if (!empty($tolly)) {
                    $output = ['Code' => '200', 'Info' => $tolly];
                } else {
                    $output = ['Code' => '204', 'Message' => 'No Content'];
                }
            } else {
                $output = ['Code' => '499', 'Error' => 'Insufficient data'];
            }


        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }

        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
        //$data = ['access' => $token];
        //$data['pos'] = Yii::$app->request->post('KK');
    }

    //Overview Polyline with LatLan(encode version) with mysql query
    public function actionCreate01122015()
    {
        $params = Yii::$app->request->post();
        $data_array = json_decode($params['latlng']);
        $data_trip = Yii::$app->request->post('trip_type');
        $vechical_types_id = Yii::$app->request->post('vechical_type');
        $user_id = Yii::$app->request->post('user_id');

        if (!empty($data_array) && !empty($data_trip) && !empty($vechical_types_id) && !empty($user_id)) {
            $data_array = json_decode($params['latlng']);
            $vec_det = $this->vechical_details($params['vechical_type'], $params);

            if ($this->succes) {
                $vechical_id = $vec_det->vechical_id;
                $vechical_types_id = $vec_det->vechical_type_id;
                if ($data_trip == 2) {
                    $trip_cost = "{{tbl_TollCosts}}.round_trip_cost AS trip_cost";
                } else {
                    $trip_cost = "{{tbl_toll_costs}}.single_trip_cost As trip_cost";
                }
                $i = 0;

                foreach ($data_array as $key => $value) {
                    $places = array();
                    $poly_points = $value->overview_polyline;
                    $points = [];
                    //print_r($value->route_id); exit;
                    $point = $this->getTolls($poly_points, $vechical_types_id, $vechical_id, $trip_cost);
                    $place = array();


                    $tolly[$key]['route_id'] = $value->route_id;


                    $tolly[$key]['total_cost'] = $point['cost'];
                    unset($point['cost']);
                    $tolly[$key]['tolls'] = $point;


                }
                //print_r($point);
                if (!empty($tolly)) {
                    $output = ['Code' => '200', 'Info' => $tolly];
                } else {
                    $output = ['Code' => '204', 'Message' => 'No Content'];
                }
            } else {
                $output = ['Code' => '499', 'Error' => 'Insufficient data'];
            }


        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }

        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
        //$data = ['access' => $token];
        //$data['pos'] = Yii::$app->request->post('KK');
    }


    public function actionTest()
    {
        $params = Yii::$app->request->post();
        //print_r($params['polyline_overview']);
        $poly_points = json_decode($params['polyline_overview']);
        foreach ($poly_points as $values) {
            $points[] = [$values->lat, $values->lng];
        }
        $collection = new LatLngCollection($points);
        $boxer = new RouteBoxer();
        //calculate boxes with 10km distance from the line between points
        $boxes = $boxer->box($collection, $distance = 0.5);
        //print_r($boxes);
    }

    public function actionSampleweb()
    {
        header("Access-Control-Allow-Origin: *");
        if ($_POST['trip_type'] == 2) {
            $trip_cost = "tbl_toll_costs.round_trip_cost as trip_cost";
        } else {
            $trip_cost = "tbl_toll_costs.single_trip_cost as trip_cost";
        }
        $data_array = json_decode($_POST['latlng']);
        $vechical_types_id = $_POST['vechical_type'];
        $i = 0;

        foreach ($data_array as $key => $value) {
            try {
                $query = new Query;
                $query->select(['CONCAT({{tbl_tolls}}.toll_name,{{tbl_tolls}}.toll_location) AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost'])
                    ->from('tbl_tolls')
                    ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
                    ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
                    ->andWhere(['=', 'toll_status', 10])
                    ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_types_id])
                    ->andWhere(['>', 'toll_lat', $value->SWl])
                    ->andWhere(['<', 'toll_lat', $value->NEl])
                    ->andWhere(['>', 'toll_lng', $value->SWln])
                    ->andWhere(['<', 'toll_lng', $value->NEln]);
                $command = $query->createCommand();
                $data = $command->queryAll();
            } catch (ErrorException $e) {
                //Yii::warning("Division by zero.");
            }

            if (!empty($data)) {
                //echo "SELECT t.toll_location as title, t.toll_lat as latitude, t.toll_lng as longitude, tc.single_trip_cost FROM tbl_tolls t, tbl_toll_costs tc WHERE t.toll_id = tc.toll_id AND toll_lat > $value->SWl AND toll_lat < $value->NEl AND toll_lng > $value->SWln AND toll_lng < $value->NEln";
                $map_info = array('title', 'latitude', 'longitude');
                $tolls_id = array();
                //print_r($data); exit;
                foreach ($data as $keys => $values) {
                    foreach ($values as $ky => $val) {
                        if (in_array($ky, $map_info) && !in_array($values['toll_id'], $tolls_id)) {
                            $places[] = $values;
                            $tolls_id[] = $values['toll_id'];
                        }
                    }
                }

            }
            $i++;
        }
        if (!empty($places)) {
            return $places;
        } else {
            return [];
        }

    }

    public function actionMontlypasslist($id)
    {
        $data = UserMonthlyTolls::find()->joinWith(['toll', 'vechicaldetails'])->where(['tbl_user_monthly_tolls.user_id' => $id, 'tbl_vechical_details.status' => 1])->all();
        if ($data) {
            $data_array = Yii::$app->myfunctions->monthly_pass($data);
            $output = ["Code" => 200, "Info" => $data_array];
        } else {
            $output = ['Code' => 204, 'Message' => 'No Content'];
        }
        Yii::$app->alog->userLog($id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }

    public function actionMonthlypasscost()
    {
        $params = Yii::$app->request->post();
        if (empty($params) || empty($params['user_id']) || empty($params['toll_id']) || empty($params['vechical_id'])) {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        } else {
            $vechicaltypeid = VechicalDetails::find()->where(['vechical_id' => $params['vechical_id']])->one();
            $monthlycost = TollCosts::find()->where(['toll_id' => $params['toll_id'], 'vechical_types_id' => $vechicaltypeid->vechical_type_id])->andWhere(['<>', 'monthly_type_id', 1])->asArray()->one();
            if (!empty($monthlycost)) {
                $monthlytype = MonthlyTypes::find()->where(['monthly_type_id' => $monthlycost['monthly_type_id']])->one();
                $monthlycost['monthly_type_name'] = (!empty($monthlytype->type_name)) ? $monthlytype->type_name : $monthlytype['type_name'];
                $monthlycost['monthly_type_description'] = (!empty($monthlytype->type_description)) ? $monthlytype->type_description : $monthlytype['type_description'];
                $output = ['Code' => 200, 'Info' => $monthlycost];
            } else {
                $output = ['Code' => 206, 'Message' => 'No Records'];
            }
        }
        return $output;
    }

    public function actionRegiontolls()
    {
        $params = Yii::$app->request->post();

        $trip_cost = "{{tbl_toll_costs}}.single_trip_cost As trip_cost";
        $data = $this->getTollsByRadius($params['lat'], $params['lng'], $params['vechical_types_id'], $params['vechical_id'], $trip_cost, 20, 100);
        if (!empty($data)) {
            $output = ['Code' => 200, 'Info' => $data];
        } else {
            $output = ['Code' => 206, 'Message' => 'No Records'];
        }
        return $output;
    }

    public function actionCreatemonthlypass()
    {
        $data_array = array();
        $params = Yii::$app->request->post();
        if (empty($params) || empty($params['user_id']) || empty($params['toll_id']) || empty($params['vechical_id']) || empty($params['valid_from']) || empty($params['transation_id'])) {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        } else {
            $output = Yii::$app->myfunctions->create_monthly($params);
            /* $vechical = VechicalDetails::find()->where(['user_id'=>$params['user_id'],'vechical_id'=>$params['vechical_id']])->one();
             if($vechical){
                 $tolls = json_decode($params['toll_id']);
                 $total = 0;
                 $params['valid_till'] = date("Y-m-d", strtotime($params['valid_from']. ' + 30 days'));
                 $total = 0;
                 $error = false;
                 foreach ($tolls as $key => $value) {
                     $monthlycost = TollCosts::findOne(['toll_id' => $value->toll_id, 'vechical_types_id' => VechicalDetails::findOne(['vechical_id' => $params['vechical_id']])->vechical_type_id]);
                     if($monthlycost) {
                         $model = new UserMonthlyTolls();
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
                         }
                         $payments[] = [$model->user_monthly_tolls_id, $monthlycost->toll_cost_id, $monthlycost->monthly_cost,  1, 3, 1, date('Y-m-d H:i:s')];
                         $total = $total + $monthlycost->monthly_cost;
                     }else{
                         $no_pass[]['toll_id'] = $value->toll_id;
                         $output['Error']=['Message'=>'This tolls donot have monthly pass' ,'No Pass' => $no_pass];
                     }
                 }

                 if($total != 0){
                     $payme = Yii::$app->db->createCommand()->batchInsert(Payments::tableName(),['trip_id' , 'toll_cost_id', 'amount', 'status_payed', 'trip_cost_type' , 'pass_type', 'created_on'],$payments)->execute();
                     $wmodel = new WalletTransactions();
                     $wmodel->attributes = ['user_id' => $params['user_id'], 'transation_id' => $params['transation_id'], 'amount' => $total, 'transation_type' => 30, 'created_on' => date('Y-m-d H:i:s')];
                     if($payme && $wmodel->save()){
                         $mopass = UserMonthlyTolls::find()->where(['user_id' => $params['user_id']])->asArray()->all();
                         $mopass = $mopass;
                         $output = ['Code' => 200, 'Info' => $mopass];

                     }else{
                         $output = ['Code' => 489, 'Error' => 'Something went wrong try again'];
                     }
                 }
             }else{
                 $output = ['Code' => 488, 'Error' => 'Vechical input is wrong'];
             }*/

        }
        $userid = 0;

        if (!empty($output->Code) && $output->Code == 200) {
            $userid = $output->Code[0]->user_id;
        }
        Yii::$app->alog->userLog($userid, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionUpdate($id)
    {
        // User::updateAll($attributes, $condition);
        return User::findOne($id);
    }

    public function actionDelete()
    {
        return ['kk' => 'sample'];
    }

    public function actionOptions($id)
    {
        return $_POST['latlng'];
    }

    public function prepareDataProvider()
    {
        // prepare and return a data provider for the "index" action
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
    }

    private function getTolls($poly_points, $vechical_types_id, $vechical_id, $trip_cost)
    {
        $toll_ids = [];
        $cost = 0;
        $point = [];
        $prelat = 0;
        $prelng = 0;
        $from_lat = 0;
        $from_lng = 0;
        $j = 0;
        foreach ($poly_points as $values) {

            if (!empty($lati) && !empty($lngi)) {
                $prelat = $lati;
                $prelng = $lngi;
            }
            $lati = $values->lat;
            $lngi = $values->lng;
            $dis = 0.05;
            if (!empty($prelng)) {
                $distance = Yii::$app->myfunctions->distance($prelat, $prelng, $lati, $lngi);
                /*echo '.......';*/
                if ($distance > 0.1) {
                    $dis = 0.1;
                }
                if ($distance > 0.2) {
                    $dis = 0.3;
                }
                if ($distance > 0.5) {
                    $dis = 0.5;
                }
                if ($distance > 1) {
                    $dis = 1;
                }
                if ($distance > 2) {
                    $dis = 2;
                }
                if ($distance > 5) {
                    $dis = 5;
                }
                if ($distance > 10) {
                    $dis = 10;
                }
                if ($distance > 20) {
                    $dis = 20;
                }
            } else {
                $from_lat = $values->lat;
                $from_lng = $values->lng;
            }
            //if ($values->lat == 12.54499) {

            //echo '<pre>';
            //echo $distance = Yii::$app->myfunctions->distance($from_lat, $from_lng, $values->lat, $values->lng);
            //echo $values->lat . "," . $values->lng;

            $query = new Query;
            $query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "( 3959 * acos( cos( radians({$values->lat}) ) * cos( radians( {{tbl_tolls}}.toll_lat ) ) * cos( radians( {{tbl_tolls}}.toll_lng) - radians({$values->lng}) ) + sin( radians({$values->lat}) ) * sin( radians( {{tbl_tolls}}.toll_lat ) ) ) ) AS dista", $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity", 'IFNULL((select  concat("[",group_concat(GRP),"]") FROM (select concat(\'{ "lat":\',lat,\',"lng":\',lng,\',"direction_id":\',direction_id,\'}\') as GRP, toll_id FROM tbl_toll_reference_points) as tmp   WHERE toll_id = tbl_tolls.toll_id),\'[]\') as reference_points'])
                //$query->select([ '{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "( 3959 * acos( cos( radians({$values->lat}) ) * cos( radians( {{tbl_tolls}}.toll_lat ) ) * cos( radians( {{tbl_tolls}}.toll_lng) - radians({$values->lng}) ) + sin( radians({$values->lat}) ) * sin( radians( {{tbl_tolls}}.toll_lat ) ) ) ) AS dista", $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
                ->from('tbl_tolls')
                ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
                ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
                ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_types_id])
                ->andWhere(['=', 'toll_status', 10])
                //->andWhere("`tbl_tollsa`.`toll_lat` BETWEEN {$values->lat} - (10 / 69) AND {$values->lat} + (10 / 69) AND `tbl_tolls`.`toll_lng` BETWEEN $values->lng - (10 / (69 * COS(RADIANS({$values->lat})))) AND {$values->lat} + (10 / (69* COS(RADIANS({$values->lat}))))")
                ->having("`dista` < $dis")
                ->limit(4);
            $command = $query->createCommand();
            $data = $command->queryAll();


            if (!empty($data)) {

                foreach ($data as $key => $value) {

                    if (!in_array($value['toll_id'], $toll_ids)) {
                        $distance = Yii::$app->myfunctions->distance($from_lat, $from_lng, $lati, $lngi);
                        //print_r($data);
                        $toll_ids[] = $value['toll_id'];
                        $place[$j] = (array)$value;
                        $place[$j]['distance'] = $distance;
                        $cost = (int)$cost + (int)$value['trip_cost'];
                        $j++;

                    }
                }

            }
            //$points[]=[$values->lat,$values->lng];
        }
        $place['cost'] = $cost;
        return $place;
    }

    private function getTollsByRadius($lat, $lng, $vechical_types_id, $vechical_id, $trip_cost, $dis = 20, $limit = 4)
    {
        //print_r($lat); exit;
        $query = new Query;
        $query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "( 3959 * acos( cos( radians({$lat}) ) * cos( radians( {{tbl_tolls}}.toll_lat ) ) * cos( radians( {{tbl_tolls}}.toll_lng) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( {{tbl_tolls}}.toll_lat ) ) ) ) AS dista", $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity", 'IFNULL((select  concat("[",group_concat(GRP),"]") FROM (select concat(\'{ "lat":\',lat,\',"lng":\',lng,\',"direction_id":\',direction_id,\'}\') as GRP, toll_id FROM tbl_toll_reference_points) as tmp   WHERE toll_id = tbl_tolls.toll_id),\'[]\') as reference_points'])
            //$query->select([ '{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "( 3959 * acos( cos( radians({$values->lat}) ) * cos( radians( {{tbl_tolls}}.toll_lat ) ) * cos( radians( {{tbl_tolls}}.toll_lng) - radians({$values->lng}) ) + sin( radians({$values->lat}) ) * sin( radians( {{tbl_tolls}}.toll_lat ) ) ) ) AS dista", $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
            ->from('tbl_tolls')
            ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
            ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
            ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_types_id])
            ->andWhere(['=', 'toll_status', 10])
            //->andWhere("`tbl_tollsa`.`toll_lat` BETWEEN {$values->lat} - (10 / 69) AND {$values->lat} + (10 / 69) AND `tbl_tolls`.`toll_lng` BETWEEN $values->lng - (10 / (69 * COS(RADIANS({$values->lat})))) AND {$values->lat} + (10 / (69* COS(RADIANS({$values->lat}))))")
            ->having("`dista` < $dis")
            ->limit($limit);
        $command = $query->createCommand();
        return $command->queryAll();
    }

    private function decodePolylineToArray($encoded, $trip_cost, $user_id, $vechical_id, $vechical_types_id)
    {
        $length = strlen($encoded);
        $index = 0;
        $points = array();
        $lat = 0;
        $lng = 0;
        $prelat = 0;
        $prelng = 0;

        while ($index < $length) {
            // Temporary variable to hold each ASCII byte.
            $b = 0;

            // The encoded polyline consists of a latitude value followed by a
            // longitude value.  They should always come in pairs.  Read the
            // latitude value first.
            $shift = 0;
            $result = 0;
            do {
                // The `ord(substr($encoded, $index++))` statement returns the ASCII
                //  code for the character at $index.  Subtract 63 to get the original
                // value. (63 was added to ensure proper ASCII characters are displayed
                // in the encoded polyline string, which is `human` readable)
                $b = ord(substr($encoded, $index++)) - 63;

                // AND the bits of the byte with 0x1f to get the original 5-bit `chunk.
                // Then left shift the bits by the required amount, which increases
                // by 5 bits each time.
                // OR the value into $results, which sums up the individual 5-bit chunks
                // into the original value.  Since the 5-bit chunks were reversed in
                // order during encoding, reading them in this way ensures proper
                // summation.
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            }
                // Continue while the read byte is >= 0x20 since the last `chunk`
                // was not OR'd with 0x20 during the conversion process. (Signals the end)
            while ($b >= 0x20);

            // Check if negative, and convert. (All negative values have the last bit
            // set)
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));

            // Compute actual latitude since value is offset from previous value.
            $lat += $dlat;

            // The next values will correspond to the longitude for this point.
            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($encoded, $index++)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
//print_r($result); exit;
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            // The actual latitude and longitude values were multiplied by
            // 1e5 before encoding so that they could be converted to a 32-bit
            // integer representation. (With a decimal accuracy of 5 places)
            // Convert back to original values.
            $path[] = array($lat * 1e-5, $lng * 1e-5);
            if (!empty($lati)) {
                $prelat = $lati;
            }
            if (!empty($lngi)) {
                $prelng = $lngi;
            }

            $lati = $lat * 1e-5;
            $lngi = $lng * 1e-5;
            $dis = 0.5;
            if (!empty($prelng)) {
                $distance = Yii::$app->myfunctions->distance($prelat, $prelng, $lati, $lngi);
                /*echo '.......';
                if($distance > 0.2){
                    $dis = 0.2;
                }*/
                if ($distance > 0.5) {
                    $dis = 0.5;
                }
                if ($distance > 1) {
                    $dis = 1;
                }
                if ($distance > 2) {
                    $dis = 2;
                }
                if ($distance > 5) {
                    $dis = 5;
                }
                if ($distance > 10) {
                    $dis = 10;
                }
                if ($distance > 20) {
                    $dis = 20;
                }
            }

//echo $dis;
            $query = new Query;
            $query->select(['{{tbl_tolls}}.toll_location AS title', '{{tbl_tolls}}.toll_name AS toll_name', '{{tbl_tolls}}.toll_lat AS latitude', '{{tbl_tolls}}.toll_lng AS longitude', '{{tbl_tolls}}.toll_id', '{{tbl_monthly_cost_types}}.monthly_type_id as monthly_type_id', '{{tbl_monthly_cost_types}}.type_name as monthly_type_name', '{{tbl_monthly_cost_types}}.type_description as monthly_type_description', $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "( 3959 * acos( cos( radians($lati) ) * cos( radians( {{tbl_tolls}}.toll_lat ) ) * cos( radians( {{tbl_tolls}}.toll_lng) - radians($lngi) ) + sin( radians($lati) ) * sin( radians( {{tbl_tolls}}.toll_lat ) ) ) ) AS dista", $trip_cost, '{{tbl_toll_costs}}.single_trip_cost', '{{tbl_toll_costs}}.round_trip_cost', '{{tbl_toll_costs}}.monthly_cost', "IF((SELECT COUNT(*) FROM tbl_user_monthly_tolls WHERE vechical_id = '$vechical_id' AND toll_id = {{tbl_tolls}}.toll_id AND (NOW() BETWEEN valid_from AND valid_till)) = 0,'NO', 'Yes') as monthly_pass_activity"])
                ->from('tbl_tolls')
                ->andWhere(['=', 'toll_status', 10])
                ->leftJoin('tbl_toll_costs', 'tbl_tolls.toll_id = tbl_toll_costs.toll_id')
                ->leftJoin('tbl_monthly_cost_types', 'tbl_toll_costs.monthly_type_id = tbl_monthly_cost_types.monthly_type_id')
                ->andWhere(['=', 'tbl_toll_costs.vechical_types_id', $vechical_types_id])
                ->having("`dista` < $dis")
                ->limit(4);
            $command = $query->createCommand();
            $point = $command->queryAll();
//print_r($point);
            if (!empty($point)) {
                foreach ($point as $key => $value) {
                    if (!in_array($value['toll_id'], $this->toll_ids)) {
                        $points['tolls'][] = $value;
                        $this->toll_ids[] = $value['toll_id'];
                    }
                }
            }
        }
//exit;
        $points['path'] = $path;
        return $points;
    }

    public function decodePolylineToArraynew($encoded)
    {
        $length = strlen($encoded);
        $index = 0;
        $points = array();
        $lat = 0;
        $lng = 0;
        while ($index < $length) {
            // Temporary variable to hold each ASCII byte.
            $b = 0;
            // The encoded polyline consists of a latitude value followed by a
            // longitude value.  They should always come in pairs.  Read the
            // latitude value first.
            $shift = 0;
            $result = 0;
            do {
                // The `ord(substr($encoded, $index++))` statement returns the ASCII
                //  code for the character at $index.  Subtract 63 to get the original
                // value. (63 was added to ensure proper ASCII characters are displayed
                // in the encoded polyline string, which is `human` readable)
                $b = ord(substr($encoded, $index++)) - 63;
                // AND the bits of the byte with 0x1f to get the original 5-bit `chunk.
                // Then left shift the bits by the required amount, which increases
                // by 5 bits each time.
                // OR the value into $results, which sums up the individual 5-bit chunks
                // into the original value.  Since the 5-bit chunks were reversed in
                // order during encoding, reading them in this way ensures proper
                // summation.
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            }
                // Continue while the read byte is >= 0x20 since the last `chunk`
                // was not OR'd with 0x20 during the conversion process. (Signals the end)
            while ($b >= 0x20);
            // Check if negative, and convert. (All negative values have the last bit
            // set)
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            // Compute actual latitude since value is offset from previous value.
            $lat += $dlat;
            // The next values will correspond to the longitude for this point.
            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($encoded, $index++)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;
            // The actual latitude and longitude values were multiplied by
            // 1e5 before encoding so that they could be converted to a 32-bit
            // integer representation. (With a decimal accuracy of 5 places)
            // Convert back to original values.
            $points[] = array($lat * 1e-5, $lng * 1e-5);
        }
        return $points;
    }


    public function actionCtollslist(){
        $toll_user_id = Yii::$app->request->post('toll_user_id');
        $user = TollUsers::findIdentity($toll_user_id);
        if(!empty($user->group_id)){
            $data = Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->all();
        }else {
            $data = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->all();
        }
        //$data = Tolls::find()->where(['toll_status' => 10])->andWhere(['like','toll_location',$id])->all();
        if ($data) {
            $output = ["Code" => 200, "Info" => $data];
        } else {
            $output = ['Code' => 204, 'Message' => 'No Content'];
        }
        //Yii::$app->alog->userLog($id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($id), json_encode($output)]);
        return $output;
    }
}
