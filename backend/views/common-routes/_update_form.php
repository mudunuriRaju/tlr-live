<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">

    <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'destination1')->textInput(['maxlength' => 45]) ?>
    <?= $form->field($model, 'destination2')->textInput(['maxlength' => 256]) ?>
    <?= $form->field($model, 'destination_1_lat')->textInput() ?>
    <?= $form->field($model, 'destination_2_lat')->textInput() ?>
    <?= $form->field($model, 'destination_1_lng')->textInput() ?>
    <?= $form->field($model, 'destination_2_lng')->textInput() ?>
    <?= $form->field($model, 'waypoints')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
