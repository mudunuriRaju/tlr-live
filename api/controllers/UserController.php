<?php

namespace api\controllers;

use api\models\MasterVechicalTypes;
use api\models\Tripdetails;
use api\models\UserDetails;
use api\models\UserTripTollExtraPayments;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use api\models\User;
use alexgx\phpexcel;
use api\models\VechicalDetails;
use api\models\MasterLanguage;
use yii\swiftmailer;
use yii\mail;
use yii\helpers\Url;
use api\models\WalletTransactions;
use api\models\UserMonthlyTolls;

/**
 * Site controller
 */
class UserController extends Controller
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
        date_default_timezone_set('asia/kolkata');
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
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
                $data['user_details']['language'] = $data['user_details']['language'][0]->laguage_name;
                $data['extra_payments'] = $this->extra_payments($user->user_id);
                $data['user_location_details'] = UserDetails::find()->where(['user_id' => $user->user_id])->asArray()->all();
                $data['vehicle_details'] = VechicalDetails::find()->where(['user_id' => $user->user_id, 'status' => 1])->asArray()->all();
                $data['monthly_pass'] = UserMonthlyTolls::find()->where(['user_id' => $user->user_id])->asArray()->all();
                $data['langauges_list'] = MasterLanguage::find()->where(['status' => 10])->asArray()->all();
                $data['vechical_types'] = MasterVechicalTypes::find()->where(['status' => 10])->asArray()->all();
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

    private function extra_payments($user_id)
    {
        $extrapayments = [];
        $extra_payments = Tripdetails::find()->joinWith(['toll', 'extrapayments'])->where(['tbl_user_trip_toll_extra_payments.pay_status' => '0', 'tbl_user_trip_toll_extra_payments.user_id' => $user_id])->asArray()->all();
        foreach ($extra_payments as $key => $value) {
            $extrapayments[$key]['toll_name'] = $value['toll']['toll_name'];
            $extrapayments[$key]['trip_details_id'] = $value['trip_details_id'];
            $extrapayments[$key]['pay_status'] = $value['extrapayments']['pay_status'];
            $extrapayments[$key]['created_on'] = $value['extrapayments']['created_on'];
            $extrapayments[$key]['amount'] = $value['extrapayments']['amount'];
        }
        return $extrapayments;
    }

    public function actionChangepassword()
    {
        $user_id = Yii::$app->request->post('user_id');
        $params = Yii::$app->request->post();
        $oldpassword = Yii::$app->request->post('oldpassword');
        $newpassword = Yii::$app->request->post('newpassword');
        $confirmpassword = Yii::$app->request->post('confirmpassword');
        if (!empty($newpassword) && !empty($user_id) && !empty($oldpassword)) {
            $user = User::findIdentity($user_id);
            $hashcode = Yii::$app->security->generatePasswordHash(Yii::$app->request->post('newpassword'));
            if ($user && $user->validatePassword($oldpassword) && ($newpassword == $confirmpassword)) {
                User::updateAll(['password' => $newpassword, 'password_hash' => $hashcode], ['user_id' => $user_id]);
                $output = ['Code' => 200, 'Message' => 'Password Changed Successfully'];
            } else {
                $output = ['Code' => 494, 'Error' => 'Problem with details provided'];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($user_id, [Url::canonical(), date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionResetpassword()
    {
        $params = Yii::$app->request->post();
        $user_email = Yii::$app->request->post('user_email');
        $password = Yii::$app->request->post('password');
        $reset_code = Yii::$app->request->post('reset_code');
        if (!empty($user_email) && !empty($password) && !empty($reset_code)) {
            $hashcode = Yii::$app->security->generatePasswordHash(Yii::$app->request->post('password'));;
            $user = User::findOne(['user_email' => $user_email, 'reset_code' => $reset_code]);
            if (!empty($user)) {
                User::updateAll(['password' => $password, 'password_hash' => $hashcode, 'reset_code' => ''], ['user_id' => $user->user_id]);
                $output = ['Code' => 200, 'Message' => 'Password Changed Successfully'];
            } else {
                $output = ['Code' => 497, 'Error' => 'email or reset code error'];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        $userid = 0;
        if (!empty($user->user_id)) {
            $userid = $user->user_id;
        }
        Yii::$app->alog->userLog($userid, [Url::canonical(), date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionSendresetpassword()
    {
        $param = Yii::$app->request->post();
        $user_email = Yii::$app->request->post('user_email');
        $user = User::findByUsername(Yii::$app->request->post('user_email'));
        if (!empty($user_email) && !empty($user)) {
            $reset_code = rand(1000, 9999); //$user->user_id . uniqid() . $user->user_id;
            User::updateAll(['reset_code' => $reset_code, 'password' => ''], ['user_id' => $user->user_id]);
            //sending email
            //Yii::$app->mailer->compose('contact/html', ['contactForm' => $user])
            //Yii::$app->mailer->compose()
            $sms_message = $mail_body = 'Your Tollr account verfication reset code is: ' . $reset_code . '. Enter this in our app to reset your Tollr accoount password';
            Yii::$app->mailer->compose('layouts/html', ['content' => $mail_body])
                ->setFrom(['donotreply@tollr.world' => 'Tollr Reset Code'])
                ->setTo($user->user_email)
                ->setSubject('Reset Passcode for Tollr')
                ->send();
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
            $output = ['Code' => 200, 'Message' => 'Reset Password sent to your email, please check it out'];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        $userid = 0;
        if (!empty($user->user_id)) {
            $userid = $user->user_id;
        }
        Yii::$app->alog->userLog($userid, [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        return $output;
    }

    public function actionIndex()
    {
        $id = 1;
        $list = ['asa', 'asd', 'asd'];
        Yii::$app->alog->userLog($id, $list);
        Yii::$app->alog->uservechicallog($id, $list);
        Yii::$app->alog->useraddresslog($id, $list);
        return [Yii::$app->params['logPath']];
        return
            [Yii::getAlias('@common')];
        $url_to_save = "log/{$id}/" . date('Y') . '/' . date('W');

        return new ActiveDataProvider([
            'query' => \api\models\User::find(),
        ]);
    }

    public function actionView($id)
    {
        $user = User::findIdentity($id);
        if (!empty($user)) {
            return $user;
        }
        return ['Code' => '204', 'Message' => 'No Content'];
    }

    public function actionCreate()
    {
        //date_default_timezone_set('asia/kolkata');
        //return Yii::$app->request->post();
        $date = date('Y-m-d H:i:s', strtotime(' +1 day'));
        $params = Yii::$app->request->post();
        $params['language_id'] = 1;
        $params['otp'] = rand(1000, 9999);
        $params['address_name'] = "Home";
        $params['address_type'] = 1;
        $params['mobile'] = $params['mobile_number'];
        //Code for US

        $model = new User();
        $model->setscenario('create');
        $model->attributes = $params;
        $model->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        //return $model;
        //print_r($model); exit;
        if ($model->save()) {
            $params['user_id'] = $model->user_id;
            if (!empty($params['us']) && $params['us'] == 1) {
                User::updateAll(['access_token' => hash('sha256', $model->user_email), 'expiry_date' => $date, 'login_status' => 2], ['user_id' => $model->user_id]);


            } else {
                User::updateAll(['access_token' => hash('sha256', $model->user_email), 'expiry_date' => $date], ['user_id' => $model->user_id]);
            }

            if ($this->setUserdetails($params)) {
                if (!empty($params['us']) && $params['us'] == 1) {
                    $user = User::find()->joinWith('language')->where(['user_id' => $model->user_id])->one();
                    $user->language_id = $user->language[0]->laguage_name;
                    $data['user_details'] = $user->getAttributes(['user_id', 'firstname', 'lastname', 'mobile_number', 'user_email', 'profile_pic', 'driving_licence', 'access_token', 'language_id', 'language', 'login_status', 'amount']);
                    $data['user_details']['language'] = $data['user_details']['language'][0]->laguage_name;
                    $data['user_location_details'] = UserDetails::find()->where(['user_id' => $user->user_id])->asArray()->all();
                    $data['extra_payments'] = $this->extra_payments($user->user_id);
                    $data['vehicle_details'] = VechicalDetails::find()->where(['user_id' => $user->user_id, 'status' => 1])->asArray()->all();
                    $data['monthly_pass'] = UserMonthlyTolls::find()->where(['user_id' => $user->user_id])->asArray()->all();
                    $data['langauges_list'] = MasterLanguage::find()->where(['status' => 10])->asArray()->all();
                    $data['vechical_types'] = MasterVechicalTypes::find(['status' => 10])->asArray()->all();
                    $output = ['Code' => 200, 'Info' => $data];

                } else {
                    $output = ['Code' => 200, 'Info' => $model];
                }
                //$output = ['Code' => 200, 'Info' => $model];
            } else {
                User::deleteAll(['user_id' => $model->user_id]);
                $output = ['Code' => 499, 'Error' => 'Insufficient data'];
            }
            $sms_message = $mail_body = 'Your Tollr account verfication OTP is: ' . $params['otp'] . '. Enter this in our app to confirm your Tollr account';
            $sms_mobile = $params['mobile'];
            $sms_message = urlencode($sms_message);
            $url = "https://alerts.sinfini.com/api/v3/index.php?method=sms&api_key=A02cd64a770e413dc100f473d732ca680&to=$sms_mobile&sender=TOLLRI&message=$sms_message&format=json&custom=1&flash=0";
            if (empty($params['us'])) {
                $curl = curl_init();
                // Set some options -
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                ));

                // Send the request & save response to $resp
                $resp = curl_exec($curl);
                // Close request to clear up
                try {
                    Yii::$app->mailer->compose('layouts/html', ['content' => $mail_body])
                        ->setFrom(['donotreply@tollr.world' => 'Tollr OTP'])
                        ->setTo($model->user_email)
                        ->setSubject('OTP for Tollr Login')
                        ->send();
                } catch (\Swift_TransportException $exception) {

                }
                unset($params['us']);
            }


        }
        //return $output;
        //return $params;
        if (!empty($params) && !empty($params['firstname']) && !empty($params['lastname']) && !empty($params['user_email']) && !empty($params['password']) && !empty($params['mobile_number']) && empty($output)) {
            $output = ['Code' => 495, 'Error' => 'Email already in use'];
        } elseif (empty($model->user_id)) {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        $userid = 0;
        if (!empty($model->user_id)) {
            $userid = $model->user_id;
        }
        Yii::$app->alog->userLog($userid, [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    public function actionUpdate($id)
    {
        $param = Yii::$app->request->post();
        if (!empty($param) && !empty($param['firstname']) && !empty($param['lastname']) && !empty($param['user_email'])) {
            if (!empty($_FILES)) {
                $target_file = basename($_FILES["profile_pic"]["name"]);
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image

                $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    $error_image = "File is not an image.";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $_FILES["profile_pic"]["size"] > 500000) {
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $error_image = "Sorry, your file was not uploaded. Error in file type or size ";
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], Yii::$app->params['pofilepicPath'] . '/user_' . $id . ".$imageFileType")) {
                        $param['profile_pic'] = 'user_' . $id . ".$imageFileType";

                    } else {
                        $error_image = "Sorry, there was an error uploading your file.";
                    }
                }
            }
            $set_array = ['firstname' => $param['firstname'], 'lastname' => $param['lastname'], 'user_email' => $param['user_email'], 'driving_licence' => (!empty($param['driving_licence'])) ? $param['driving_licence'] : 0];
            if (!empty($param['profile_pic'])) {
                $set_array['profile_pic'] = $param['profile_pic'];
            }
            User::updateAll($set_array, ['user_id' => $id]);
            $user_details = UserDetails::find()->where(['user_id' => $id, 'address_type' => 1])->one();
            //print_r($user_details); exit;
            if (!empty($param['address1'])) {
                UserDetails::updateAll(['address1' => $param['address1'], 'address2' => !empty($param['address2']) ? $param['address2'] : $user_details['address2'], 'zipcode' => !empty($param['zipcode']) ? $param['zipcode'] : $user_details['zipcode'], 'state' => !empty($param['state']) ? $param['state'] : $user_details['state'], 'city' => !empty($param['city']) ? $param['city'] : $user_details['city'], 'mobile' => !empty($param['mobile']) ? $param['mobile'] : $user_details['mobile']], ['user_id' => $id, 'address_type' => 1]);
            }

            if (empty($error_image)) {
                $output = ["Code" => 200, "Info" => User::findIdentity($id), 'Message' => 'Profile pic uploaded successfully Or Not attempted to upload pic', 'user_location_details' => UserDetails::find()->where(['user_id' => $id])->asArray()->all()];
                //$output ['Info']['user_location_details'] = UserDetails::find()->where(['user_id' => $id])->asArray()->all();
            } else {
                $output = ["Code" => 200, "Info" => User::findIdentity($id), 'Error' => $error_image];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->userLog($id, [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        return $output;
    }

    public function actionUserdetails()
    {
        $params = Yii::$app->request->post();
        //Yii::$app->alog->useraddresslog($params['user_id'], [date('Y-m-d'),json_encode($params),'add']);
        $model = new UserDetails();
        $model->attributes = $params;
        if ($model->save()) {
            $output = ["Code" => 200, "Info" => UserDetails::findAll(['user_id' => $params['user_id']])];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->useraddresslog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output), 'add']);
        Yii::$app->alog->userLog($params['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($params), json_encode($output)]);
        return $output;
    }

    private function setUserdetails($params)
    {
        $model = new UserDetails();
        $model->attributes = $params;
        if ($model->save()) {
            return $model;
        } else {
            return false;
        }
    }


    public function actionUserdetailsdelete()
    {
        $param = Yii::$app->request->post();
        //Yii::$app->alog->useraddresslog($param['user_id'], [date('Y-m-d'),json_encode($param),'delete']);
        if (!empty($param['user_id']) && !empty($param['address_name']) && !empty($param['address_type'])) {
            $result = UserDetails::deleteAll($param);
            if ($result) {
                $output = ["Code" => 200, "Info" => UserDetails::findAll(['user_id' => $param['user_id']])];
            } else {
                $output = ['Code' => 493, "Error" => "Something went wrong try again"];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        Yii::$app->alog->useraddresslog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output), 'delete']);
        Yii::$app->alog->userLog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:s:i'), json_encode($param), json_encode($output)]);
        return $output;
    }


    public function actionCashin()
    {
        $param = Yii::$app->request->post();
        if (!empty($param['amount'])) {
            $wallet = new WalletTransactions();
            $wallet->attributes = $param;
            $wallet->transation_type = 10;
            $wallet->created_on = date('Y-m-d H:i:s');
            $amount = User::findone(['user_id' => $param['user_id']]);
            if ($wallet->save()) {
                $amount = $amount->amount + $param['amount'];
                $user = User::updateAll(['amount' => $amount], ['user_id' => $param['user_id']]);
                if ($user) {
                    $output = ['code' => 200, 'Message' => 'Cash added to Your Wallet Sucessfully', 'amount' => $amount];
                } else {
                    $output = ['Code' => 493, "Error" => "Something went wrong try again"];
                }
            } else {
                $output = ['code' => 499, 'Error' => 'Insuffient Data'];
            }
        } else {
            $amount = User::findone(['user_id' => $param['user_id']]);
            $output = ['code' => 200, 'Message' => 'Cash added to Your Wallet Sucessfully', 'amount' => $amount->amount];
        }
        Yii::$app->alog->userLog($param['user_id'], [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($param), json_encode($output)]);
        return $output;
    }

    public function actionDelete($id)
    {
        User::updateAll(['status' => 0], ['user_id' => $id]);
        return ['Code' => 200, 'message' => 'User has been successfully deleted.'];
    }

    public function actionVerifyotp()
    {
        date_default_timezone_set('asia/kolkata');
        $params = Yii::$app->request->post();
        if (!empty($params['user_id']) && !empty($params['otp'])) {
            $user = User::find()->andWhere(['user_id' => $params['user_id']])->andWhere(['otp' => $params['otp']])->andWhere(['<', 'MINUTE(TIMEDIFF(NOW(),`otp_created_on`))', 15])->one();
            if (empty($user)) {
                $params['otp'] = rand(1000, 9999);
                $model = new User();
                $model->attributes = $params;
                $user = User::find()->andWhere(['user_id' => $params['user_id']])->one();
                //echo date('Y-m-d H:i:s'); exit;
                if (User::updateAll(['otp' => $params['otp'], 'otp_created_on' => date('Y-m-d H:i:s')], ['user_id' => $params['user_id']])) {
                    $sms_message = $mail_body = 'Your Tollr account verfication OTP is: ' . $params['otp'] . '. Enter this in our app to confirm your Tollr account';
                    Yii::$app->mailer->compose('layouts/html', ['content' => $mail_body])
                        ->setFrom(['donotreply@tollr.world' => 'Tollr OTP'])
                        ->setTo($user->user_email)
                        ->setSubject('OTP for Tollr Login')
                        ->send();
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
                    $output = ['Code' => 491, 'Error' => "Your OTP is expired and new OTP is sent to your email. Please try with new one."];
                } else {
                    $output = ['Code' => 499, 'Error' => 'Insufficient data'];
                }

            } else {
                User::updateAll(['login_status' => 2], ['user_id' => $params['user_id']]);
                $user = User::find()->joinWith('language')->where(['user_id' => $params['user_id']])->one();
                $user->language_id = $user->language[0]->laguage_name;
                $data['user_details'] = $user->getAttributes(['user_id', 'firstname', 'lastname', 'mobile_number', 'user_email', 'profile_pic', 'driving_licence', 'access_token', 'language_id', 'language', 'login_status']);
                $data['user_details']['language'] = $data['user_details']['language'][0]->laguage_name;
                $data['user_location_details'] = UserDetails::find()->where(['user_id' => $params['user_id']])->asArray()->all();
                $data['extra_payments'] = $this->extra_payments($user->user_id);
                $data['vehicle_details'] = VechicalDetails::find()->where(['user_id' => $params['user_id'], 'status' => 1])->asArray()->all();
                $data['monthly_pass'] = UserMonthlyTolls::find()->where(['user_id' => $user->user_id])->asArray()->all();
                $data['langauges_list'] = MasterLanguage::find()->where(['status' => 10])->asArray()->all();
                $data['vechical_types'] = MasterVechicalTypes::find(['status' => 10])->asArray()->all();
                $output = ['Code' => 200, 'Info' => $data];
            }
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        return $output;
    }

    public function actionOptions($id)
    {
        echo $id;
        print_r($_POST);
        echo 'asd';
        exit;
    }

    public function actionExtrapayments()
    {
        $params = Yii::$app->request->post();
        $model = new WalletTransactions();
        $params['created_on'] = date('Y-m-d H:i:s');
        $model->attributes = $params;
        if ($model->save()) {
            $status = UserTripTollExtraPayments::updateAll(['pay_status' => 1], ['user_id' => $params['user_id'], 'trip_details_id' => $params['trip_details_id'], 'amount' => $params['amount'], 'transation_id' => $params['transation_id']]);
            $output = ['Code' => 200, 'Message' => 'Status Updated Sucessfully'];
        } else {
            $output = ['Code' => 499, 'Error' => 'Insufficient data'];
        }
        return $output;
    }


    public function prepareDataProvider()
    {
        // prepare and return a data provider for the "index" action
    }

    public function actionAddamount()
    {
        $param = Yii::$app->request->post();
    }

    public function actionExceptionapi()
    {
        $param = Yii::$app->request->post();
        try {
            Yii::$app->alog->userException([Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($param)]);
            return ['Code' => 200, 'Message' => 'Successfully added'];
        } catch (ErrorException $e) {
            return ['Code' => 499, 'Error' => 'Error'];
        }


    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
    }

    public function actionPingcheck()
    {
        return ['Code' => 200, 'success' => TRUE];
    }

//    public function beforeAction($login) {
//        return true;
////        echo 'asda';
////        exit;
//    }
}
