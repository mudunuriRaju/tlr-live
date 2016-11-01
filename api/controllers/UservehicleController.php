<?php

namespace api\controllers;

use api\models\VechicalDetails;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use api\models\Vehicle;
use api\models\User;
use yii\swiftmailer;
use yii\mail;
use yii\helpers\Url;


//use yii\mail\BaseMailer;

/**
 * Site controller
 */
class UservehicleController extends Controller
{


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
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

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
        return new ActiveDataProvider([
            'query' => VechicalDetails::find(),
        ]);
    }

    public function actionCreate()
    {
        $params['user_id'] = 0;
        $params = Yii::$app->request->post();

        if (!empty($params) && !empty($params['registration_no']) && !empty($params['user_id']) && !empty($params['vechical_type_id']) && isset($params['use_type']) && !empty($params['vechical_nickname'])) {
            if (!empty($params['timezone'])) {
                unset($params['timezone']);
            }
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
                            $params[$key] = $model->$key = $file_image;
                        } else {
                            $error_msg = "Problem with uploaded file";
                        }
                    }
                }
            }

            if ($model->save()) {
                if (empty($error_msg)) {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $params['user_id'], 'status' => 1])->all(), 'Message' => 'file uploaded successfully'];
                } else {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $params['user_id'], 'status' => 1])->all(), 'Error' => $error_msg];
                }
            } else {
                //print_r($model->errors['vechical_id']);
                if (!empty($model->errors) && !empty($model->errors['vechical_id'])) {
                    $params['status'] = 1;
                    VechicalDetails::updateAll($params, ['vechical_id' => $vid]);
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $params['user_id'], 'status' => 1])->all(), 'Message' => 'file uploaded successfully'];
                } elseif (empty($uid['user_id'])) {
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

    public function actionUpdate($id)
    {
        $user_id = 0;
        $param = Yii::$app->request->post();
        $information = VechicalDetails::findOne(['vechical_id' => $id]);
        //print_r($information); exit;
        if (!empty($param) && !empty($param['vechical_type_id']) && !empty($param['vechical_nickname']) && !empty($information)) {
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
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $param['user_id'], 'status' => 1])->all(), 'Message' => 'file uploaded successfully'];
                } else {
                    $output = ["Code" => 200, "Info" => VechicalDetails::find()->where(['user_id' => $param['user_id'], 'status' => 1])->all(), 'Error' => $error_msg];
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

    public function actionDelete()
    {
        $param['user_id'] = 0;
        $param = Yii::$app->request->post();
        if (!empty($param['user_id']) && !empty($param['vechical_id'])) {
            if (!empty($param['timezone'])) {
                unset($param['timezone']);
            }
            $model = VechicalDetails::find()->where($param)->one();
            $model->updateAll(['status' => 0], ['vechical_id' => $param['vechical_id']]);
            //$model->delete();
            //VechicalDetails::deleteAll($param);
            //VechicalDetails::deleteAll('user_id = :uid AND registration_no = :regno', [':uid' => $param['user_id'], ':regno' => $param['registration_no']]);
            $output = ['Code' => 200, 'Info' => VechicalDetails::findAll(['user_id' => $param['user_id'], 'status' => 1])];
        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        Yii::$app->alog->uservechicallog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($param), json_encode($output), 'delete']);
        return $output;
    }

    public function actionView($id)
    {
        if (!empty($id) && is_numeric($id)) {
            $output = ['Code' => 200, 'Info' => VechicalDetails::findAll(['user_id' => $id, 'status' => 1])];
        } else {
            $output = ['Code' => '499', 'Error' => 'Insufficient data'];
        }
        return $output;

    }

}
