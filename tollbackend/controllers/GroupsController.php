<?php

namespace tollbackend\controllers;

use Yii;
use tollbackend\models\Groups;
use tollbackend\models\GroupSearch;
use tollbackend\models\TollUsers;
use tollbackend\models\TollUserSearch;
use tollbackend\models\MasterLanguage;
use tollbackend\models\MasterTollUserTypes;
use yii\helpers\ArrayHelper;
use tollbackend\models\Tolls;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GroupsController implements the CRUD actions for Groups model.
 */
class GroupsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Creates a new Groups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Groups();
        if ($model->load(Yii::$app->request->post())) {

            $group = Yii::$app->request->post('Groups');
            $postkey = $group['group_key'];
            $group_key = $this->findModel($group);
            $gkey = $group_key['group_key'];
            if ($gkey == $postkey) {
                $gid = $group_key['toll_group_id'];
                $user = TollUsers::findIdentity(Yii::$app->user->id);
                $tid = $user['toll_id'];
                $update = Tolls::updateAll(['group_id' => $gid], 'toll_id =' . $tid . '');
                $updatetollusers = TollUsers::updateAll(['group_id' => $gid], 'toll_id =' . $tid . '');
                return $this->redirect('../');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);


    }


    /**
     * Finds the Groups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groups::findOne($id)) !== null) {
            return $model;
        } else {
            //throw new NotFoundHttpException('The requested page does not exist.');
            return $model;
        }
    }
}
