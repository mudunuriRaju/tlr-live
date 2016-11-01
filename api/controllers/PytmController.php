<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use common\models\LoginForm;
use api\models\User;

/**
 * Site controller
 */
class PytmController extends Controller
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
    { //return [Yii::$app()->controller->action->id];
        $behaviors = parent::behaviors();
        //$action = Yii::app()->controller->id;
        //if ( $action == 'vali') {
        //    $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        // }else {
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        // }
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
    }


    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => \api\models\User::find(),
        ]);
    }

    public function actionView($id)
    {
        return \api\models\User::findIdentity($id);
    }

    public function actionCreate()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");

        $checkSum = "";
        $paramList = array();
        $paramList = Yii::$app->request->post();
        $required_list = ["CHANNEL_ID", "CUST_ID", "EMAIL", "INDUSTRY_TYPE_ID", "MID", "MOBILE_NO", "ORDER_ID", "THEME", "TXN_AMOUNT", "WEBSITE"];
        $error = true;
        foreach ($required_list as $value) {
            if (empty($paramList[$value])) {
                $error = true;
            }
        }
        if ($error) {
            // Create an array having all required parameters for creating checksum.
            //Here checksum string will return by getChecksumFromArray() function.
            $checkSum = Yii::$app->pytm->getChecksumFromArray($paramList);
            //print_r($_POST);
            Yii::$app->alog->userLog('a', [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode(array("CHECKSUMHASH" => $checkSum, "ORDER_ID" => $paramList["ORDER_ID"], "payt_STATUS" => "1"))]);
            //echo json_encode();
            return array("CHECKSUMHASH" => $checkSum, "ORDER_ID" => $paramList["ORDER_ID"], "payt_STATUS" => "1");
        } else {
            return ["payt_STATUS" => "2"];
        }
    }

    public function actionUpdate($id)
    {
        // User::updateAll($attributes, $condition);
        // return User::findOne($id);
    }

    public function actionVali()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");

        $paytmChecksum = "";
        $paramList = array();
        $isValidChecksum = FALSE;

        $checksum = '';
        $paramList = Yii::$app->request->post();
        $return_array = Yii::$app->request->post();
        $checksum = Yii::$app->request->post("CHECKSUMHASH");

        $paytmChecksum = isset($checksum) ? $checksum : ""; //Sent by Paytm pg

//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application’s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
        $isValidChecksum = Yii::$app->pytm->verifychecksum_e($paramList, $paytmChecksum); //will return TRUE or FALSE string.

        if ($isValidChecksum === TRUE)
            $return_array["IS_CHECKSUM_VALID"] = "Y";
        else
            $return_array["IS_CHECKSUM_VALID"] = "N";

//$return_array["IS_CHECKSUM_VALID"] = $isValidChecksum ? "Y" : "N";
        $return_array["TXNTYPE"] = "";
        $return_array["REFUNDAMT"] = "";
        unset($return_array["CHECKSUMHASH"]);
//return $return_array;
        //$encoded_json = htmlentities(json_encode($return_array));
        $encoded_json = json_encode($return_array);

//============  Sample json response passed to SDK after verifying checksum  ==================================

//    { "TXNID": "4203335",    "BANKTXNID": "",    "ORDERID": "ORDER1409950517",    "TXNAMOUNT": "1",    "STATUS": TXN_SUCCESS",    "TXNTYPE": "",    "CURRENCY": "INR",    "GATEWAYNAME": "ICICI",    "RESPCODE": "01",    "RESPMSG": "Txn Successfull.",    "BANKNAME": "HDFC",    MID": "robosf49909586699899",    "PAYMENTMODE": "CC",    "REFUNDAMT": "",    "TXNDATE": "2013­04­19 14:35:50.775483",    "IS_CHECKSUM_VALID": "Y"}
//$this->behaviors();
        //$behaviors = parent::behaviors();
        Yii::$app->response->format = 'html';
        echo "<html>
<head>
    <meta http-equiv='Content-Type' content='text/html;charset=ISO-8859-I'>
    <title>Paytm</title>
    <script type='text/javascript'>
        function response(){
            return document.getElementById('response').value;
        }
    </script>
</head>
<body>
    Redirect back to the app<br>

<form name='frm' method='post'>

    <input type='hidden' id='response' name='responseField' value='$encoded_json'>
</form>
</body>
</html>";
        Yii::$app->alog->userLog('v', [Yii::$app->request->absoluteUrl, date('Y-m-d H:i:s'), json_encode($paramList)]);

    }

    public function actionDelete()
    {
        return ['kk' => 'sample'];
    }

    public function actionOptions()
    {
        //echo $id;
        print_r($_POST);
        echo 'asd';
        exit;
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


    public function actionCitrus()
    {
        $txn_id = "TX" . "Tlr" . time() . rand(10000, 99999);
        $value = Yii::$app->request->post("amount"); //Charge amount is in INR by default ($_GET["amount"])
        $data_string = "merchantAccessKey=" . Yii::$app->params['caccess_key']
            . "&transactionId=" . $txn_id
            . "&amount=" . $value;
        $signature = hash_hmac('sha1', $data_string, Yii::$app->params['csecret_key']);
        $amount = array('value' => $value, 'currency' => 'INR');
        $bill = array('merchantTxnId' => $txn_id,
            'amount' => $amount,
            'requestSignature' => $signature,
            'merchantAccessKey' => Yii::$app->params['caccess_key'],
            'returnUrl' => Yii::$app->params['creturn_url']);
        return $bill;
        //echo json_encode($bill);
    }

    public function actionCreturnurl()
    {
        $secret_key = Yii::$app->params['csecret_key'];
        $data = array();
        echo '<pre>';
        print_r($_SERVER);
        exit;
        if (!empty($_POST)) {
            $request_para = $_POST;
        } else {
            $request_para = $_GET;
        }
        foreach ($request_para as $name => $value) {
            $data[$name] = $value;

        }
        /*$verification_data = "";
        if(!empty($data['TxId'])){
            $verification_data .= $data['TxId'];
        }
        if(!empty($data['TxStatus'])){
            $verification_data .= $data['TxStatus'];
        }
        if(!empty($data['amount'])){
            $verification_data .= $data['amount'];
        }
        if(!empty($data['pgTxnNo'])){
            $verification_data .= $data['pgTxnNo'];
        }
        if(!empty($data['issuerRefNo'])){
            $verification_data .= $data['issuerRefNo'];
        }
        if(!empty($data['authIdCode'])){
            $verification_data .= $data['authIdCode'];
        }
        if(!empty($data['firstName'])){
            $verification_data .= $data['firstName'];
        }
        if(!empty($data['lastName'])){
            $verification_data .= $data['lastName'];
        }
        if(!empty($data['pgRespCode'])){
            $verification_data .= $data['pgRespCode'];
        }
        if(!empty($data['addressZip'])){
            $verification_data .= $data['addressZip'];
        }*/
        $trns_id = "";
        if (!empty($data['TxId'])) {
            $trns_id = $data['TxId'];
        }
        $verification_data = $trns_id
            . $data['TxStatus']
            . $data['amount']
            . $data['pgTxnNo']
            . $data['issuerRefNo']
            . $data['authIdCode']
            . $data['firstName']
            . $data['lastName']
            . $data['pgRespCode']
            . $data['addressZip'];
        $signature = hash_hmac('sha1', $verification_data, $secret_key);
        if ($signature == $data['signature']) {
            $json_object = json_encode($data);
            $pres = "<script> postResponse('$json_object'); </script>";
            $pset = "<script> setdata ('$json_object'); </script>";
            $iosres = "<script> postResponseiOS(); </script>";
        } else {
            $response_data = array("Error" => "Transaction Failed",
                "Reason" => "Signature Verfication Failed");
            $json_object = json_encode($response_data);
            $pres = "<script> postResponse('$json_object'); </script>";
            $pset = "<script> setdata ('$json_object'); </script>";
            $iosres = "<script> postResponseiOS(); </script>";
        }
        Yii::$app->response->format = 'html';

        echo "<html>
  <head>
  <script type='text/javascript'>
	var globaldata;
    function setdata(data) {
        globaldata = data;
    }
    function postResponseiOS() {
        return globaldata;
    }
    function postResponse(data) {
        CitrusResponse.pgResponse(data);
    }
   </script>
   </head>
   <body>
   $pres
   $pset
   </body>
   </html>  ";

    }


    public function actionCitruss()
    {
        $txn_id = "TX" . "Tlr" . time() . rand(10000, 99999);
        $value = Yii::$app->request->post("amount"); //Charge amount is in INR by default ($_GET["amount"])
        $data_string = "merchantAccessKey=" . Yii::$app->params['caccess_key_S']
            . "&transactionId=" . $txn_id
            . "&amount=" . $value;
        $signature = hash_hmac('sha1', $data_string, Yii::$app->params['csecret_key_S']);
        $amount = array('value' => $value, 'currency' => 'INR');
        $bill = array('merchantTxnId' => $txn_id,
            'amount' => $amount,
            'requestSignature' => $signature,
            'merchantAccessKey' => Yii::$app->params['caccess_key_S'],
            'returnUrl' => Yii::$app->params['creturn_url_S']);
        return $bill;
        //echo json_encode($bill);
    }

    public function actionCreturnurls()
    {
        $secret_key = Yii::$app->params['csecret_key_S'];
        $data = array();
        echo '<pre>';
        print_r($_SERVER);
        exit;
        if (!empty($_POST)) {
            $request_para = $_POST;
        } else {
            $request_para = $_GET;
        }
        foreach ($request_para as $name => $value) {
            $data[$name] = $value;

        }
        /*$verification_data = "";
        if(!empty($data['TxId'])){
            $verification_data .= $data['TxId'];
        }
        if(!empty($data['TxStatus'])){
            $verification_data .= $data['TxStatus'];
        }
        if(!empty($data['amount'])){
            $verification_data .= $data['amount'];
        }
        if(!empty($data['pgTxnNo'])){
            $verification_data .= $data['pgTxnNo'];
        }
        if(!empty($data['issuerRefNo'])){
            $verification_data .= $data['issuerRefNo'];
        }
        if(!empty($data['authIdCode'])){
            $verification_data .= $data['authIdCode'];
        }
        if(!empty($data['firstName'])){
            $verification_data .= $data['firstName'];
        }
        if(!empty($data['lastName'])){
            $verification_data .= $data['lastName'];
        }
        if(!empty($data['pgRespCode'])){
            $verification_data .= $data['pgRespCode'];
        }
        if(!empty($data['addressZip'])){
            $verification_data .= $data['addressZip'];
        }*/
        $trns_id = "";
        if (!empty($data['TxId'])) {
            $trns_id = $data['TxId'];
        }

        //print_r($data);
        //exit(0);

        $verification_data = $trns_id
            . $data['TxStatus']
            . $data['amount']
            . $data['pgTxnNo']
            . $data['issuerRefNo']
            . $data['authIdCode']
            . $data['firstName']
            . $data['lastName']
            . $data['pgRespCode']
            . $data['addressZip'];
        $signature = hash_hmac('sha1', $verification_data, $secret_key);
        if ($signature == $data['signature']) {
            $json_object = json_encode($data);
            $pres = "<script> postResponse('$json_object'); </script>";
            $pset = "<script> setdata ('$json_object'); </script>";
            $iosres = "<script> postResponseiOS(); </script>";
        } else {
            $response_data = array("Error" => "Transaction Failed",
                "Reason" => "Signature Verfication Failed");
            $json_object = json_encode($response_data);
            $pres = "<script> postResponse('$json_object'); </script>";
            $pset = "<script> setdata ('$json_object'); </script>";
            $iosres = "<script> postResponseiOS(); </script>";
        }
        Yii::$app->response->format = 'html';

        echo "<html>
  <head>
  <script type='text/javascript'>
	var globaldata;
    function setdata(data) {
        globaldata = data;
    }
    function postResponseiOS() {
        return globaldata;
    }
    function postResponse(data) {
        CitrusResponse.pgResponse(data);
    }
   </script>
   </head>
   <body>
   $pres
   $pset
   </body>
   </html>  ";

    }

    public function actionCareturnurl()
    {
        echo '<pre>';
        print_r($_SERVER);
        exit;

        $response_data = array("Sucess" => "Success",
            "Reason" => "IOS only");
        $json_object = json_encode($response_data);
        $iosres = "<script> postResponseiOS(); </script>";
        $pres = "<script> postResponse('$json_object'); </script>";
        $pset = "<script> setdata ('$json_object'); </script>";
        Yii::$app->response->format = 'html';

        echo "<html>
  <head>
  <script type='text/javascript'>
	var globaldata;
    function setdata(data) {
        globaldata = data;
    }
    function postResponseiOS() {
        return globaldata;
    }
    function postResponse(data) {
        CitrusResponse.pgResponse(data);
    }
   </script>
   </head>
   <body>
   $pres
   $pset
   $iosres
   </body>
   </html>  ";
    }

//    public function beforeAction($login) {
//        return true;
////        echo 'asda';
////        exit;
//    }
}
