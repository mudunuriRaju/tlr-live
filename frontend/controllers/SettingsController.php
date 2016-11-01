<?php
namespace frontend\controllers;

use Yii;
use frontend\models\Settings;
use frontend\models\SettingsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Security;

class SettingsController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SettingsSearch();
        $type = 3;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('faq', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Settings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAboutus()
    {
        $searchModel = new SettingsSearch();
        $type = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('aboutus', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTermslist()
    {
        $searchModel = new SettingsSearch();
        $type = 2;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('terms', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrivacy()
    {
        $searchModel = new SettingsSearch();
        $type = 4;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('privacy', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function ProcessCurl($URL, $fieldString)
    {
        //Initiate Curl request and send back the result
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldString);
        $resulta = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        } else {
            curl_close($ch);
        }
        return $resulta;
    }
}
