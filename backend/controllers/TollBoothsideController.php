<?php

namespace backend\controllers;

use Yii;
use backend\models\TollBoothside;
use backend\models\TollBoothsideSearch;
use backend\models\TollSidesForm;
use backend\models\Tolls;
use backend\models\TollUsers;
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
                    ],
                    [
                        'actions' => ['index', 'view'],
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
     * Lists all TollBoothside models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $params = Yii::$app->request->queryParams;
        $params['TollBoothsideSearch']['toll_id'] = $id;
        $searchModel = new TollBoothsideSearch();
        $dataProvider = $searchModel->search($params);

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
    public function actionCreate($id)
    {
        /*$user = TollUsers::findIdentity(Yii::$app->user->id);
        if(!empty($user->group_id)){
           $tolls = Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls,'toll_id','toll_location');
        }else {
            $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls,'toll_id','toll_location');
        }*/
        //$user= TollBoothside::find()->where(['boothside_id' => $id])->one();
        $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $id])->asArray(true)->one();
        $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location');
        //print_r($dataTolls); exit;

        $params = Yii::$app->request->post();
        if (!empty($params)) {
            foreach ($params['TollBoothside']['boothside_from'] as $key => $value) {
                $model = new TollBoothside();
                $model->toll_id = $params['TollBoothside']['toll_id'];
                $model->boothside_from = $value;
                $model->lat = $params['TollBoothside']['lat'][$key];
                $model->lng = $params['TollBoothside']['lng'][$key];
                $model->setScenario('create');
                $model->save();
            }
            //print_r(Yii::$app->request->post()); exit;
            //$model->load(Yii::$app->request->post()) && $model->save()
            Yii::$app->getSession()->setFlash('msg', 'Toll Booth Sides added Successfully');
            return $this->redirect(['toll-booths/create?id=' . $id]);
            // //return $this->redirect(['view', 'id' => $model->boothside_id]);
        } else {
            $model = new TollBoothside();


            //$tolls =ArrayHelper::map($tolls,'toll_lat','toll_lng','toll_id');
            $model->toll_id = $tolls['toll_id'];
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
        /*$user = TollUsers::findIdentity(Yii::$app->user->id);
        if(!empty($user->group_id)){
            $tolls = Tolls::find()->where(['tbl_tolls.group_id' => $user->group_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls,'toll_id','toll_location');
        }else {
            $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->all();
            $dataTolls = ArrayHelper::map($tolls,'toll_id','toll_location','toll_lat');
        }*/
        $user = TollBoothside::find()->where(['boothside_id' => $id])->one();
        $tolls = Tolls::find()->where(['tbl_tolls.toll_id' => $user->toll_id])->asArray(true)->one();
        $dataTolls = ArrayHelper::map($tolls, 'toll_id', 'toll_location', 'toll_lat');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Booth Sides updated Successfully');
            return $this->redirect(['index?id=' . $model->toll_id]);
            //return $this->redirect(['view', 'id' => $model->boothside_id]);
        } else {
            //$tolls =ArrayHelper::map($tolls,'toll_lat','toll_lng','toll_id');
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
