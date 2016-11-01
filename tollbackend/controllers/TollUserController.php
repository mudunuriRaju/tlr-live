<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\TollUsers;
use tollbackend\models\TollUserSearch;
use tollbackend\models\MasterLanguage;
use tollbackend\models\MasterTollUserTypes;
use tollbackend\models\Tolls;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * TollUserController implements the CRUD actions for TollUsers model.
 */
class TollUserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->toll_user_type_id == 1;
                        }
                    ],
                    [
                        'actions' => ['update', 'create', 'index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->toll_user_type_id <= 2);
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
     * Lists all TollUsers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TollUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TollUsers model.
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
     * Creates a new TollUsers model.
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
        $model = new TollUsers();
        $dataLanguages = ArrayHelper::map(MasterLanguage::find()->asArray(true)->all(), 'lagunage_id', 'laguage_name');
        $dataUsertypes = ArrayHelper::map(MasterTollUserTypes::find()->where(['!=', 'toll_user_type_id', '1'])->all(), 'toll_user_type_id', 'type_name');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->toll_password_hash = Yii::$app->security->generatePasswordHash($model->toll_password);
            if (!empty($user->group_id)) {
                $model->group_id = $user->group_id;
            }
            $model->save();
            Yii::$app->getSession()->setFlash('msg', 'Toll User added Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->toll_user_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataLanguages' => $dataLanguages,
                'dataUsertypes' => $dataUsertypes,
                'dataTolls' => $dataTolls
            ]);
        }
    }

    /**
     * Updates an existing TollUsers model.
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
        $dataLanguages = ArrayHelper::map(MasterLanguage::find()->asArray(true)->all(), 'lagunage_id', 'laguage_name');
        $dataUsertypes = ArrayHelper::map(MasterTollUserTypes::find()->where(['!=', 'toll_user_type_id', '1'])->all(), 'toll_user_type_id', 'type_name');

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('msg', 'Toll User updated Successfully');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->toll_user_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dataLanguages' => $dataLanguages,
                'dataUsertypes' => $dataUsertypes,
                'dataTolls' => $dataTolls
            ]);
        }
    }

    /**
     * Deletes an existing TollUsers model.
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
     * Finds the TollUsers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TollUsers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TollUsers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
