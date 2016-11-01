<?php
namespace backend\controllers;

use Yii;
use backend\models\Settings;
use backend\models\SettingsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Security;


class SettingsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SettingsSearch();
        $type = 3;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('index', [
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

    public function actionCreate()
    {
        $model = new Settings();
        if ($model->load(Yii::$app->request->post())) {
            $model->type = 3;
            $model->save();
            Yii::$app->getSession()->setFlash('msg', 'Faq Created Successfully');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Faq Updated Successfully');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('msg', 'Faq deleted Successfully');
        return $this->redirect(['index']);
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


    public function actionTermsadd()
    {
        $model = new Settings();
        if ($model->load(Yii::$app->request->post())) {
            $model->type = 2;
            $model->save();
            Yii::$app->getSession()->setFlash('msg', 'Terms Created Successfully');
            return $this->redirect(['termslist']);
        } else {
            return $this->render('termsadd', [
                'model' => $model,
            ]);
        }
    }

    public function actionTermslist()
    {
        $searchModel = new SettingsSearch();
        $type = 2;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('termslist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrivacy()
    {
        $searchModel = new SettingsSearch();
        $type = 4;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $type);
        return $this->render('privacy_policy', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function ProcessCurl($URL, $fieldString)
    { //Initiate Curl request and send back the result
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
