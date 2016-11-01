<?php
namespace frontend\controllers;

use Yii;
use frontend\models\LoginForm;
use frontend\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\VechicalDetails;
use frontend\models\MasterVechicalTypes;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    //public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'user'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'user'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    //'user' => ['post']
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new LoginForm();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {

        //if (!\Yii::$app->user->isGuest) {
        //    return $this->goHome();
        //}

        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        //print_r($model); exit;
        //$model = Yii::$app->request->post();
        //print_r(Yii::$app->request->post()); exit;
        echo '<pre>';

        if ($model->login()) {
            //print_r(Yii::$app->user->identity);
            return $this->redirect('user');
        } else {
            print_r($model->errors);
            exit;
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionUser()
    {
        //print_r(Yii::$app->user->identity->user_id); exit;
        $from_date = DATE('Y-m-1');
        $to_date = DATE('Y-m-d');
        $user_id = Yii::$app->user->identity->user_id;
        $command = Yii::$app->db->createCommand("CALL TripHistoryList($user_id,'$from_date','$to_date')");
        $history = (array)$command->queryAll();
        $command = Yii::$app->db->createCommand("CALL TripFavList($user_id,'$from_date','$to_date')");
        $favs = (array)$command->queryAll();
        $vechicals = (array)VechicalDetails::find()->where(['user_id' => $user_id])->joinWith('vtype')->all();
        $vechicals = ArrayHelper::toArray($vechicals);
        //$vechicaal_types = MasterVechicalTypes::find()->all();
        $vechicaal_types = ArrayHelper::toArray(MasterVechicalTypes::find()->all());
        $vechical_types = ArrayHelper::index(ArrayHelper::toArray(MasterVechicalTypes::find()->all()), 'vechical_types_id', 'type');
        $vehical_list = ArrayHelper::index($vechicals, 'vechical_id');
        //print_r($vechicaal_types); exit;
        return $this->render('user', ['history' => $history, 'favs' => $favs, 'vechicals' => $vechicals, 'vechicaal_types' => $vechicaal_types, 'vechical_types' => $vechical_types, 'vehical_list' => $vehical_list]);
    }
}
