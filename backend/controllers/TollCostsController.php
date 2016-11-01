<?php

namespace backend\controllers;

use Yii;
use backend\models\TollCosts;
use backend\models\TollCostsSearch;
use backend\models\Tolls;
use backend\models\TollUsers;
use backend\models\MasterVechicalTypes;
use backend\models\MonthlyTypes;
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
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@']
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
    public function actionIndex($id)
    {
        $params = Yii::$app->request->queryParams;
        $params['TollCostsSearch']['toll_id'] = $id;
        $searchModel = new TollCostsSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id' => $id
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
    public function actionCreate($id)
    {

        $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.toll_id' => $id])->asArray(true)->all(), 'toll_id', 'toll_location');
        $params = Yii::$app->request->post();
        if (empty($params['number_monthly_trips'])) {
            $params['number_monthly_trips'] = 0;
        }
        //echo $params['number_monthly_trips']; exit;
        $dataVehicletypes = ArrayHelper::map(MasterVechicalTypes::find()->where(['=', 'status', '10'])->all(), 'vechical_types_id', 'type');
        $dataMonthlytypes = ArrayHelper::map(MonthlyTypes::find()->where(['=', 'status', '10'])->all(), 'monthly_type_id', 'type_name');
        $dataDirection = ArrayHelper::map(\api\models\TollBoothside::find()->where(['toll_id' => $id])->asArray(true)->all(), 'boothside_id', 'boothside_from');
        $dataDirection[0] = 'Both Directions';
        //print_r($dataDirection); exit;
        $model = new TollCosts();
        $model->toll_id = $id;
        //$model->load($params);

        if ($model->load($params) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Cost added Successfully');
            return $this->redirect(['index', 'id' => $id]);
            //return $this->redirect(['view', 'id' => $model->toll_cost_id]);
        } else {
            $model->number_monthly_trips = 0;
            return $this->render('create', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'dataVehicletypes' => $dataVehicletypes,
                'dataMonthlytypes' => $dataMonthlytypes,
                'dataDirection' => $dataDirection,
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
        $model = $this->findModel($id);
        $dataTolls = ArrayHelper::map(Tolls::find()->where(['tbl_tolls.toll_id' => $model->toll_id])->asArray(true)->all(), 'toll_id', 'toll_location');
        $dataVehicletypes = ArrayHelper::map(MasterVechicalTypes::find()->where(['=', 'status', '10'])->all(), 'vechical_types_id', 'type');
        $dataMonthlytypes = ArrayHelper::map(MonthlyTypes::find()->where(['=', 'status', '10'])->all(), 'monthly_type_id', 'type_name');
        $dataDirection = ArrayHelper::map(\api\models\TollBoothside::find()->where(['toll_id' => $id])->asArray(true)->all(), 'boothside_id', 'boothside_from');
        $dataDirection[0] = 'Both Directions';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Cost updated Successfully');
            return $this->redirect(['index?id=' . $model->toll_id]);
            //return $this->redirect(['view', 'id' => $model->toll_cost_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dataTolls' => $dataTolls,
                'dataVehicletypes' => $dataVehicletypes,
                'dataMonthlytypes' => $dataMonthlytypes,
                'dataDirection' => $dataDirection
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
