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


class Apilog extends Component
{

    public $userLogPath;

    function userLog($id, $list)
    {
        $url_to_save = "api/{$id}/" . date('Y') . '/' . date('W');
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);
        $files = array_diff(scandir($this->userLogPath . "api/{$id}/" . date('Y')), array('..', '.'));
        foreach ($files as $file) {

            if ($file < date('W') - 1 && is_dir($this->userLogPath . "api/{$id}/" . date('Y') . "/" . $file)) {

                exec("tar -czf $this->userLogPath" . "api/{$id}/" . date('Y') . "/$file.tar.gz $this->userLogPath" . "api/{$id}/" . date('Y') . "/" . $file);
                exec("rm -r $this->userLogPath" . "api/{$id}/" . date('Y') . "/" . $file);
            }

        }
    }

    function userException($list)
    {
        $url_to_save = "api/Exception/" . date('Y-m-d');
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);

    }

    function uservechicallog($id, $list)
    {
        $url_to_save = "{$id}";
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/vechical_log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);
    }

    function useraddresslog($id, $list)
    {
        $url_to_save = "{$id}";
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/address_log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);
    }

    function tolluserLog($id, $list)
    {
        $url_to_save = "tollapi/{$id}/" . date('Y') . '/' . date('W');
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);
    }

    function directionuserLog($id, $list)
    {
        $url_to_save = "direction_log/{$id}/" . date('Y-m-d');
        if (!is_dir($this->userLogPath . $url_to_save)) {
            mkdir($this->userLogPath . $url_to_save, 0777, true);
            chmod($this->userLogPath . $url_to_save, 0777);
        }
        clearstatcache();
        $fp = fopen($this->userLogPath . $url_to_save . "/log_file.csv", 'a+');
        fputcsv($fp, $list);
        fclose($fp);
    }

}