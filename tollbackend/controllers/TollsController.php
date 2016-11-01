<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\Tolls;
use yii\filters\AccessControl;
use tollbackend\models\TollUsers;
use tollbackend\models\TollSearch;
use tollbackend\models\TollForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LanguagesController implements the CRUD actions for MasterLanguage model.
 */
class TollsController extends Controller
{


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'error'],
                        'allow' => false,
                    ],
                    [
                        'actions' => ['update', 'index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id < 2;
                        }
                    ]
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

    /**
     * Lists all MasterLanguage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TollSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        echo '<pre>';
//        print_r($dataProvider->count);
//        exit;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MasterLanguage model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the MasterLanguage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MasterLanguage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tolls::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Creates a new Tolls model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TollForm();
        if ($model->load(Yii::$app->request->post())) {
            $toll = Yii::$app->request->post('TollForm');
            $tolluser_array = ['toll_employee_id', 'toll_user_type_id', 'toll_password', 'status', 'language_id'];
            $model1 = new Tolls();
            $model2 = new TollUsers();
            foreach ($toll as $key => $value) {
                if (in_array($key, $tolluser_array)) {
                    $model2->$key = $value;
                } else {
                    $model1->$key = $value;
                }
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model1->save();
                $model1->link('tollUsers', $model2); // <-- it creates new record in tollUsers table with model2.toll_id = model1.toll_id

                $jsonpost = '{
                                "location": {
                                    "lat": ' . $model1->toll_lat . ',
                                    "lng": ' . $model1->toll_lng . '
                                },
                                "accuracy": 50,
                                "name": "NHAi transit station, ' . $model1->toll_location . '",
                                "types": ["establishment"],
                            }';
                $url = "https://maps.googleapis.com/maps/api/place/add/json?sensor=false&key=" . Yii::$app->params['google_api_key'];
                $results = $this->ProcessCurl($url, $jsonpost);
                $place_id = json_decode($results);
                $model1->place_id = $place_id->place_id;
                $model1->save();
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
            }
            return $this->redirect(['view', 'id' => $model1->toll_id]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Tolls model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $lat = $model->toll_lat;
        $lng = $model->toll_lng;
        $place = $model->place_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($lat != $model->toll_lat && $lat != $model->toll_lng) {
                $jsonpost = '{
                                "location": {
                                    "lat": ' . $model->toll_lat . ',
                                    "lng": ' . $model->toll_lng . '
                                },
                                "accuracy": 50,
                                "name": "NHAi transit station, ' . $model->toll_location . '",
                                "types": ["establishment"],
                            }';
                $url = "https://maps.googleapis.com/maps/api/place/add/json?sensor=false&key=" . Yii::$app->params['google_api_key'];
                $results = $this->ProcessCurl($url, $jsonpost);
                $place_id = json_decode($results);
                $model->place_id = $place_id->place_id;
                $model->save();
                $jsonpost = '{
                                "place_id": "' . $place . '"
                            }';
                $url = "https://maps.googleapis.com/maps/api/place/delete/json?sensor=false&key=" . Yii::$app->params['google_api_key'];
                $results = $this->ProcessCurl($url, $jsonpost);
            }
            Yii::$app->getSession()->setFlash('msg', 'Toll updated Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->toll_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MasterLanguage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
