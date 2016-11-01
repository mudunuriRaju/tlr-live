<?php

namespace backend\controllers;

use Yii;
use backend\models\ReferencePoints;
use backend\models\ReferencePointsSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReferencePointsController implements the CRUD actions for ReferencePoints model.
 */
class ReferencePointsController extends Controller
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

    /**
     * Lists all ReferencePoints models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $params = Yii::$app->request->queryParams;
        $params['ReferencePointsSearch']['toll_id'] = $id;
        $searchModel = new ReferencePointsSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReferencePoints model.
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
     * Creates a new ReferencePoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {

        $params = Yii::$app->request->post();
        //print_r($params); exit;
        if (!empty($params)) {
            foreach ($params['ReferencePoints']['toll_axis'] as $key => $value) {
                if (!empty($value)) {
                    $model = new ReferencePoints();
                    $pa['ReferencePoints']['toll_id'] = $id;
                    $pa['ReferencePoints']['direction_id'] = $params['ReferencePoints']['direction_id'][$key];
                    $pa['ReferencePoints']['toll_axis'] = $value;
                    $pa['ReferencePoints']['lat'] = $params['ReferencePoints']['lat'][$key];
                    $pa['ReferencePoints']['lng'] = $params['ReferencePoints']['lng'][$key];
                    if ($model->load($pa) && $model->save()) {
                        //return $this->redirect(['view', 'id' => $model->toll_ref_point_id]);
                    }
                }
            }
            return $this->redirect(['index', 'id' => $id]);
        }
        $model = new ReferencePoints();
        $model->toll_id = $id;
        $points = ReferencePoints::find()->where(['toll_id' => $id])->count();
        $count = 6 - $points;
        if ($count == 0) {
            return $this->redirect(['tolls/index']);
        }
        return $this->render('create', [
            'model' => $model,
            'count' => $count,
        ]);

        //print_r(Yii::$app->request->post()); exit;

    }

    /**
     * Updates an existing ReferencePoints model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->toll_ref_point_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ReferencePoints model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReferencePoints model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReferencePoints the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReferencePoints::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
