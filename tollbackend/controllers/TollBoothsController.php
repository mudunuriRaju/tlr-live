<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\TollBooths;
use tollbackend\models\TollBoothsSearch;
use tollbackend\models\TollBoothside;
use tollbackend\models\TollUsers;
use tollbackend\models\Tolls;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * TollBoothsController implements the CRUD actions for TollBooths model.
 */
class TollBoothsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete', 'create', 'boothside'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id <= 2;
                        }

                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id <= 3;
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
     * Lists all TollBooths models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TollBoothsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TollBooths model.
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
     * Creates a new TollBooths model.
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
        $model = new TollBooths();
        if ($model->load(Yii::$app->request->post())) {
            $cnt = TollBooths::find()->where(['booth_side' => $model->booth_side])->count();
            $model->booth_unique_id = 1;
            if (!empty($cnt)) {
                $model->booth_unique_id = $cnt + 1;
            }

            if ($model->save()) {
                Yii::$app->getSession()->setFlash('msg', 'Toll Booth added Successfully');
                return $this->redirect(['index']);
                //return $this->redirect(['view', 'id' => $model->booth_id]);
            }

        }
        return $this->render('create', [
            'model' => $model,
            'dataTolls' => $dataTolls

        ]);

    }

    /**
     * Updates an existing TollBooths model.
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll Booth updated Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->booth_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dataTolls' => $dataTolls
            ]);
        }
    }

    /**
     * Deletes an existing TollBooths model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionBoothside($id)
    {
        $countPosts = TollBoothside::find()
            ->where(['toll_id' => $id])
            ->count();
        $booth_sides = ArrayHelper::map(TollBoothside::find()->where(['toll_id' => $id])->asArray(true)->all(), 'boothside_id', 'boothside_from');
        if ($countPosts > 0) {

            foreach ($booth_sides as $key => $value) {
                $booths = TollBooths::find()
                    ->where(['booth_side' => $key])
                    ->count();
                if ($booths == 0) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                } else {
                    if ($booths < 15) {
                        echo "<option value='" . $key . "'>" . $value . "</option>";
                    } else {
                        echo 'Already created';
                    }
                }
            }
        }

    }


    /**
     * Finds the TollBooths model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TollBooths the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TollBooths::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
