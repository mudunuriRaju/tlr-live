<?php
/**
 * Created by PhpStorm.
 * User: kesavam
 * Date: 13/5/15
 * Time: 2:41 PM
 */

namespace api\components;

use Yii;
use yii\base\Component;


class Paytm extends Component
{

    public $paytm_environment;
    public $paytm_merchant_key;
    public $paytm_merchant_mid;
    public $paytm_merchant_website;
    public $paytm_domain = "pguat.paytm.com";
    public $paytm_refund_url;
    public $paytm_status_query_url;
    public $paytm_txn_url;

    function init()
    {

        if ($this->paytm_environment == 'PROD') {
            $this->paytm_domain = 'secure.paytm.in';
        }
        $this->paytm_refund_url = 'https://' . $this->paytm_domain . '/oltp/HANDLER_INTERNAL/REFUND';
        $this->paytm_status_query_url = 'https://' . $this->paytm_domain . '/oltp/HANDLER_INTERNAL/TXNSTATUS';
        $this->paytm_txn_url = 'https://' . $this->paytm_domain . '/oltp-web/processTransaction';
    }

    function encrypt_e($input, $ky)
    {
        $key = $ky;
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
        $input = $this->pkcs5_pad_e($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
        $iv = "@@@@&&&&####$$$$";
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    function decrypt_e($crypt, $ky)
    {

        $crypt = base64_decode($crypt);
        $key = $ky;
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
        $iv = "@@@@&&&&####$$$$";
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic($td, $crypt);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $decrypted_data = $this->pkcs5_unpad_e($decrypted_data);
        $decrypted_data = rtrim($decrypted_data);
        return $decrypted_data;
    }

    function pkcs5_pad_e($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    function pkcs5_unpad_e($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        return substr($text, 0, -1 * $pad);
    }

    function generateSalt_e($length)
    {
        $random = "";
        srand((double)microtime() * 1000000);

        $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
        $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
        $data .= "0FGH45OP89";

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;
    }

    function checkString_e($value)
    {
        $myvalue = ltrim($value);
        $myvalue = rtrim($myvalue);
        if ($myvalue == 'null')
            $myvalue = '';
        return $myvalue;
    }

    function getChecksumFromArray($arrayList, $sort = 1)
    {
        if ($sort != 0) {
            ksort($arrayList);
        }
        $str = $this->getArray2Str($arrayList);
        $salt = $this->generateSalt_e(4);
        $finalString = $str . "|" . $salt;
        $hash = hash("sha256", $finalString);
        $hashString = $hash . $salt;
        $checksum = $this->encrypt_e($hashString, $this->paytm_merchant_key);
        return $checksum;
    }

    function verifychecksum_e($arrayList, $checksumvalue)
    {
        $arrayList = $this->removeCheckSumParam($arrayList);
        ksort($arrayList);
        $str = $this->getArray2Str($arrayList);
        $paytm_hash = $this->decrypt_e($checksumvalue, $this->paytm_merchant_key);
        $salt = substr($paytm_hash, -4);

        $finalString = $str . "|" . $salt;

        $website_hash = hash("sha256", $finalString);
        $website_hash .= $salt;

        $validFlag = FALSE;
        if ($website_hash == $paytm_hash) {
            $validFlag = TRUE;
        } else {
            $validFlag = FALSE;
        }
        return $validFlag;
    }

    function getArray2Str($arrayList)
    {
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            if ($flag) {
                $paramStr .= $this->checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|" . $this->checkString_e($value);
            }
        }
        return $paramStr;
    }

    function redirect2PG($paramList, $key)
    {
        $hashString = $this->getchecksumFromArray($paramList);
        $checksum = $this->encrypt_e($hashString, $key);
    }

    function removeCheckSumParam($arrayList)
    {
        if (isset($arrayList["CHECKSUMHASH"])) {
            unset($arrayList["CHECKSUMHASH"]);
        }
        return $arrayList;
    }

    function getTxnStatus($requestParamList)
    {
        return $this->callAPI($this->paytm_status_query_url, $requestParamList);
    }

    function initiateTxnRefund($requestParamList)
    {
        $CHECKSUM = $this->getChecksumFromArray($requestParamList, $this->paytm_merchant_key, 0);
        $requestParamList["CHECKSUM"] = $CHECKSUM;
        return $this->callAPI($this->paytm_refund_url, $requestParamList);
    }

    function callAPI($apiURL, $requestParamList)
    {
        $jsonResponse = "";
        $responseParamList = array();
        $JsonData = json_encode($requestParamList);
        $postData = 'JsonData=' . urlencode($JsonData);
        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData))
        );


        //Yii::$app->alog->userLog('a', [$apiURL, date('Y-m-d H:i:s'), json_encode($requestParamList)]);
        $jsonResponse = curl_exec($ch);
        $responseParamList = json_decode($jsonResponse, true);
        return $responseParamList;
    }

}