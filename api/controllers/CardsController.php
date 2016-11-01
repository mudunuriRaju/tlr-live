<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 3/9/2016
 * Time: 5:16 PM
 */

namespace api\controllers;

use api\models\CardTemp;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseStringHelper;
use yii\helpers\BaseJson;
use yii\helpers\Url;
use api\models\MasterVechicalTypes;
use api\models\UserTripTollExtraPayments;
use yii\swiftmailer;
use yii\mail;

class CardsController extends Controller
{
    //public $modelClass = 'api\models\User';
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

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */

    public function actions()
    {
        if (Yii::$app->request->post('timezone')) {
            //echo Yii::$app->request->post('timezone'); exit;
            // echo urldecode(Yii::$app->request->post('timezone')); exit;
            date_default_timezone_set(urldecode(Yii::$app->request->post('timezone')));
        }

    }

    public function actionCards()
    {
        $params = Yii::$app->request->post();
        return ['Code' => 200, 'Info' => CardTemp::find()->where(['user_id' => $params['user_id']])->all()];
    }

    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        $model = new CardTemp();
        $model->attributes = $params;
        if ($model->save()) {
            return ['Code' => 200, 'Info' => CardTemp::find()->where(['user_id' => $params['user_id']])->all()];
        } else {
            return ['Code' => 499, 'Error' => 'Insuffecient data'];
        }

    }
}