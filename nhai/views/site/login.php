<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <video autoplay loop poster="<?php echo Yii::$app->request->baseUrl; ?>/images/nhaLoginBg1.jpg" id="bgvid">
        <source src="<?php echo Yii::$app->request->baseUrl; ?>/images/driving.webm" type="video/webm">
        <source src="<?php echo Yii::$app->request->baseUrl; ?>/images/driving.mp4" type="video/mp4">
    </video>
</div>
<div id="NHAloginBox">
    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/login-logo.png" width="124" height="62" alt=""/>
    <h2>Login</h2>
    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'id' => 'noBottomBorder', 'placeholder' => "User Name"])->label(false) ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => "Password"])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton('Login', ['name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script type="application/javascript">
    var base_url = '<?php echo Yii::$app->request->baseUrl; ?>';
</script>
