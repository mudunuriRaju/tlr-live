<?php

namespace api\controllers;

use api\models\TollBoothside;
use api\models\Tolls;
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
use api\models\HistoryDateWithvechicaltypes;
use api\models\HistoryOfPayments;
use api\models\MasterVechicalTypes;

/**
 * Site controller
 */
class HistoryapiController extends Controller
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

    public function actionReports()
    {
        $params = Yii::$app->request->post();
        $and = ['and'];
        if (!empty($params['state'])) {
            $tolls = Tolls::find()->where("toll_location LIKE '%{$params['state']}%'")->select('GROUP_CONCAT(toll_id) as ids')->asArray()->one();
            if (empty($tolls['ids'])) {
                $tolls['ids'] = 0;
            }
            $and[] = "toll_id in ({$tolls['ids']})";
        }
        $select_month = 'NOW()';
        if (!empty($params['selected_month'])) {
            $select_month = "NOW() - INTERVAL {$params['selected_month']} MONTH";
        }

        $groupby = 'date';
        $select = "*";
        $data['vehical_types'] = (array)MasterVechicalTypes::find()->where(['country_id' => 105])->all();

        foreach ($data['vehical_types'] as $value) {
            $vehical_type_id = $value->vechical_types_id;
            if (empty($sum_amounts)) {

                $sum_amounts = "IFNULL(`amount_$vehical_type_id`,0)";
                $sum_counter = "IFNULL(`counter_$vehical_type_id`,0)";
            } else {
                $sum_amounts .= " + IFNULL(`amount_$vehical_type_id`,0) ";
                $sum_counter .= " + IFNULL(`counter_$vehical_type_id`,0) ";
            }
        }
        $amounts = "SUM($sum_amounts)";
        $counter = "SUM($sum_counter)";
        if ((!empty($params['type_report']) && $params['type_report'] == 2) || empty($params['toll_id'])) {

            foreach ($data['vehical_types'] as $value) {
                $select .= ", SUM(amount_{$value->vechical_types_id}) as amount_{$value->vechical_types_id}, SUM(counter_{$value->vechical_types_id}) as counter_{$value->vechical_types_id}";
            }
            if (!empty($params['type_report']) && $params['type_report'] == 2) {
                $groupby = 'MONTH(date)';
                $and[] = "YEAR(date) BETWEEN YEAR(CURDATE())-1 AND YEAR(CURDATE())";
            } else {
                $and[] = "`date` BETWEEN DATE_FORMAT($select_month,'%Y-%m-1')  AND LAST_DAY($select_month)";
            }
        } else {
            $and[] = "`date` BETWEEN DATE_FORMAT($select_month,'%Y-%m-1')  AND LAST_DAY($select_month)";
        }
        if (!empty($params['toll_id'])) {
            $and[] = "toll_id={$params['toll_id']}";
        }

        $history = HistoryDateWithvechicaltypes::find()->select($select)->where($and)->groupBy($groupby)->orderBy('date DESC')->all();
        $amount = HistoryDateWithvechicaltypes::find()->select(["$amounts as sum_amount", "$counter as sum_counter"])->where($and)->orderBy('date DESC')->asArray()->one();

        $row_count = COUNT($history);


        $data['history'] = $history;
        $data['amount'] = $amount;
        return ['Code' => 200, 'Info' => $data['history'], 'Count' => $row_count, 'Counters' => $data['amount']];
    }


}
