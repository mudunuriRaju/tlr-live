<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\MasterLanguage;
use backend\models\MasterTollUserTypes;

//use backend\models\


/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">
    <?php $dataLanguages = ArrayHelper::map(MasterLanguage::find()->asArray(true)->all(), 'lagunage_id', 'laguage_name'); ?>
    <?php $dataUsertypes = ArrayHelper::map(MasterTollUserTypes::find()->where(['toll_user_type_id' => 1])->asArray(true)->all(), 'toll_user_type_id', 'type_name'); ?>
    <?php $form = ActiveForm::begin(); ?>
    <span class="col-sm-12">
    <?= $form->field($model, 'toll_name')->textInput(['maxlength' => 45]) ?>
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
    <span class="col-sm-8">
    <?= $form->field($model, 'toll_location')->textInput(['maxlength' => 256]) ?>
    </span>
    <span class="col-sm-4">
        <?= $form->field($model, 'toll_state')->textInput(['minlength' => 8])->label('State Of Toll') ?>
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
    <span class="col-sm-6">
    <?= $form->field($model, 'toll_employee_id')->textInput() ?>
    </span>
    <span class="col-sm-6">
    <?= $form->field($model, 'toll_password')->textInput(['minlength' => 8])->label('Eployee Password') ?>
    </span>
    <span class="col-sm-6">
    <?= $form->field($model, 'toll_concessionaire')->textInput(['minlength' => 8])->label('Concessionaire Name') ?>
    </span>
    <span class="col-sm-6">
    <?= $form->field($model, 'toll_contact')->textInput(['minlength' => 8])->label('Concessionaire Contact no') ?>
    </span>
    <span class="col-sm-6">
    <?= $form->field($model, 'toll_user_type_id')->dropDownList($dataUsertypes, ['readonly' => true])->label('User type') ?>
    </span>
    <span class="col-sm-6">
        <?= $form->field($model, 'language_id')->dropDownList($dataLanguages)->label('Language') ?>
    </span>
    <span class="col-sm-6">
    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>
        </span>

    <?php ActiveForm::end(); ?>

</div>
