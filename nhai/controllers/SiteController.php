<?php
namespace nhai\controllers;


use api\models\MasterVechicalTypes;
use api\models\Tolls;
use common\components\Toll;
use nhai\models\HistoryDateWithvechicaltypes;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

use nhai\models\LoginForm;
use yii\filters\VerbFilter;
use nhai\models\HistoryOfPayments;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'reports', 'report'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionReports()
    {
        $select = "";
        for ($i = 0; $i <= 12; $i++) {
            // $data['month_options'][]= ['name'=>date("Y-m", strtotime(date('Y-m-01') . " -$i months")),'value'=>date("M/Y", strtotime(date('Y-m-01') . " -$i months"))];
            $data['month_options'][] = ['name' => $i, 'value' => date("M/Y", strtotime(date('Y-m-01') . " -$i months"))];

        }

        $params = Yii::$app->request->post();
        $data['vehical_types'] = (array)MasterVechicalTypes::find()->where(['country_id' => 105])->all();
        foreach ($data['vehical_types'] as $value) {
            $select .= ", SUM(amount_{$value->vechical_types_id}) as amount_{$value->vechical_types_id}, SUM(counter_{$value->vechical_types_id}) as counter_{$value->vechical_types_id}";
        }
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
        $data['history'] = HistoryDateWithvechicaltypes::find()->select("*, $select")->where("`date` BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()")->groupBy('date')->all();
        $data['counter'] = HistoryDateWithvechicaltypes::find()->select(["$amounts as sum_amount", "$counter as sum_counter"])->where("`date` BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()")->orderBy('date DESC')->asArray()->one();

        $data['count'] = COUNT($data['history']);
        //print_r($data); exit;
        //print_r($sample); exit;
        return $this->render('report', $data);
    }

    public function actionReport($id)
    {
        $select = "";
        for ($i = 0; $i <= 12; $i++) {
            // $data['month_options'][]= ['name'=>date("Y-m", strtotime(date('Y-m-01') . " -$i months")),'value'=>date("M/Y", strtotime(date('Y-m-01') . " -$i months"))];
            $data['month_options'][] = ['name' => $i, 'value' => date("M/Y", strtotime(date('Y-m-01') . " -$i months"))];

        }
        $params = Yii::$app->request->post();
        $data['vehical_types'] = (array)MasterVechicalTypes::find()->where(['country_id' => 105])->all();
        foreach ($data['vehical_types'] as $value) {
            $select .= ", SUM(amount_{$value->vechical_types_id}) as amount_{$value->vechical_types_id}, SUM(counter_{$value->vechical_types_id}) as counter_{$value->vechical_types_id}";
        }
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
        $data['toll_details'] = Tolls::find()->where(['toll_id' => $id])->one();
        //print_r($data['toll_details']); exit;
        $data['history'] = HistoryDateWithvechicaltypes::find()->where("`date` BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() AND toll_id=$id")->all();
        $data['vehical_types'] = (array)MasterVechicalTypes::find()->where(['country_id' => 105])->all();
        $data['counter'] = HistoryDateWithvechicaltypes::find()->select(["$amounts as sum_amount", "$counter as sum_counter"])->where("`date` BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() AND toll_id=$id")->orderBy('date DESC')->asArray()->one();
        $data['count'] = COUNT($data['history']);
        return $this->render('report', $data);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }


    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
