<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

//use backend\models\


/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">

    <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    <?php $form = ActiveForm::begin(); ?>

    <span class="col-sm-12">
    <?= $form->field($model, 'toll_name')->textInput(['maxlength' => 45]) ?>
    </span>
    <span class="col-sm-12">
    <?= $form->field($model, 'toll_location')->textInput(['maxlength' => 256]) ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'toll_lat')->textInput() ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'toll_lng')->textInput() ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'toll_radius')->textInput() ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'allowed_ip')->textInput() ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'motorway_id')->textInput() ?>
    </span>
    <span class="col-sm-4">
    <?= $form->field($model, 'toll_stretch')->textInput(['minlength' => 8]) ?>
    </span>
    <span class="col-sm-12">
    <?= $form->field($model, 'toll_status')->dropDownList(['10' => 'Active', '00' => 'De-active']) ?>
    </span>
    <span class="col-sm-12">
    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>
    </span>

    <?php ActiveForm::end(); ?>

</div>
