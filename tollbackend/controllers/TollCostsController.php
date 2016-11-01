<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\TollCosts;
use tollbackend\models\TollCostsSearch;
use tollbackend\models\Tolls;
use tollbackend\models\TollUsers;
use tollbackend\models\MasterVechicalTypes;
use tollbackend\models\MonthlyTypes;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TollCostsController implements the CRUD actions for TollCosts model.
 */
class TollCostsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'update'],
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
     * Lists all TollCosts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TollCostsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TollCosts model.
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
     * Creates a new TollCosts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all(), 'toll_id', 'toll_location');
        } else {
            $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all(), 'toll_id', 'toll_location');
        }
        $params = Yii::$app->request->post();
        if (empty($params['number_monthly_trips'])) {
            $params['number_monthly_trips'] = 0;
        }
        //echo $params['number_monthly_trips']; exit;
        $dataVehicletypes = ArrayHelper::map(MasterVechicalTypes::find()->where(['=', 'status', '10'])->all(), 'vechical_types_id', 'type');
        $dataMonthlytypes = ArrayHelper::map(MonthlyTypes::find()->where(['=', 'status', '10'])->all(), 'monthly_type_id', 'type_name');
        $model = new TollCosts();
        //$model->load($params);

        if ($model->load($params) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Cost added Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->toll_cost_id]);
        } else {
            $model->number_monthly_trips = 0;
            return $this->render('create', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'dataVehicletypes' => $dataVehicletypes,
                'dataMonthlytypes' => $dataMonthlytypes
            ]);
        }
    }

    /**
     * Updates an existing TollCosts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = TollUsers::findIdentity(Yii::$app->user->id);
        if (!empty($user->group_id)) {
            $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all(), 'toll_id', 'toll_location');
        } else {
            $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all(), 'toll_id', 'toll_location');
        }
        $dataVehicletypes = ArrayHelper::map(MasterVechicalTypes::find()->where(['=', 'status', '10'])->all(), 'vechical_types_id', 'type');
        $dataMonthlytypes = ArrayHelper::map(MonthlyTypes::find()->where(['=', 'status', '10'])->all(), 'monthly_type_id', 'type_name');
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Cost updated Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->toll_cost_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'dataVehicletypes' => $dataVehicletypes,
                'dataMonthlytypes' => $dataMonthlytypes
            ]);
        }
    }

    /**
     * Deletes an existing TollCosts model.
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
     * Finds the TollCosts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TollCosts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TollCosts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
