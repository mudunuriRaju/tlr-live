<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Tollr';
?>
<div ng-show="login">
    <div><img width="100%" height="100%" src="<?php echo Yii::$app->homeUrl; ?>images/bg.jpg"></div>
    <div class="frmBox">
        <div><img src="<?php echo Yii::$app->homeUrl; ?>images/logoBig.png"></div>
        <div class="inpFrm">
            <form name="user" method="post" action="<?php echo Yii::$app->homeUrl; ?>site/login">
                <?php $form = ActiveForm::begin(['id' => 'login-form', 'action' => Yii::$app->homeUrl . 'site/login']); ?>
                <input type="email" name="username" ng-model="user.employee_id" class="inpTxt" placeholder="Email"
                       required>
                <input type="password" name="password" ng-model="user.password" class="inpTxt" placeholder="Password"
                       required>
                <input type="submit" value="Login" class="inpBtn">

                <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>