<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\TollBoothside;
use tollbackend\models\TollBoothsideSearch;
use tollbackend\models\TollSidesForm;
use tollbackend\models\Tolls;
use tollbackend\models\TollUsers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * TollBoothsideController implements the CRUD actions for TollBoothside model.
 */
class TollBoothsideController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'update', 'boothsidecount'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id == 1;
                        }
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id <= 2;
                        }
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
     * Lists all TollBoothside models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TollBoothsideSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TollBoothside model.
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
     * Creates a new TollBoothside model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $tolls = Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location');
        } else {
            $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location');
        }
        $model = new TollBoothside();
        $model->setScenario('create');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Booth Sides added Successfully');
            return $this->redirect(['index']);
            // //return $this->redirect(['view', 'id' => $model->boothside_id]);
        } else {
            $tolls = ArrayHelper::map($tolls, 'toll_lat', 'toll_lng', 'toll_id');

            return $this->render('create', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'Tolls' => json_encode($tolls)
            ]);
        }
    }

    /**
     * Updates an existing TollBoothside model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $tolls = Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location');
        } else {
            $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location', 'toll_lat');
        }
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Booth Sides updated Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->boothside_id]);
        } else {
            $tolls = ArrayHelper::map($tolls, 'toll_lat', 'toll_lng', 'toll_id');
            //print_r($model); exit;
            return $this->render('update', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'Tolls' => json_encode($tolls)
            ]);
        }
    }

    /**
     * Deletes an existing TollBoothside model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionBoothsidecount($id)
    {
        $boothsidescount = TollBoothside::find()
            ->where(['toll_id' => $id])
            ->count();
        return $boothsidescount;

    }

    /**
     * Finds the TollBoothside model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TollBoothside the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TollBoothside::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
