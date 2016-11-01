<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $modelClass = 'api\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //echo $this->action;
//        if (Yii::app()->controller->id != 'index') {
//        $behaviors['authenticator'] = [
//            'class' => HttpBasicAuth::className(),
//            'only' => ['view']
//        ];
//        }
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
//        $actions = parent::actions();
//        unset($actions['delete'], $actions['create']);
//        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
//
//        return $actions;
    }

    public function actionLogin()
    {
        return ['kk' => 'sample'];
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => \api\models\User::find(),
        ]);
    }

    public function actionView($id)
    {
        return ['kk' => 'sample'];
    }

    public function actionCreate($token)
    {
        if ($token == 'Login') {
            $data['pos'] = Yii::$app->request->post();
            return $data;
        }
        if ($token == 'Signup') {
            $data['pos'] = 'asd';
            return $data;
        }
    }

    public function actionUpdate($id)
    {
        return User::findOne($id);
    }

    public function actionDelete()
    {
        return ['kk' => 'sample'];
    }

    public function actionOptions()
    {

    }

    public function prepareDataProvider()
    {
        // prepare and return a data provider for the "index" action
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
    }

//    public function beforeAction($login) {
//        return true;
////        echo 'asda';
////        exit;
//    }
}
