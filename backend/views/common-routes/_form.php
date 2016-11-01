<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

//use backend\models\


/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">

    <?php $form = ActiveForm::begin(); ?>
    <span class="col-sm-6">
    <?= $form->field($model, 'destination1')->textInput()->label('From') ?>
    </span>
    <span class="col-sm-3">
    <?= $form->field($model, 'destination_1_lat')->textInput()->label('Lat') ?>
        </span>
    <span class="col-sm-3">
    <?= $form->field($model, 'destination_1_lng')->textInput()->label('Lng') ?>
            </span>
    <span class="col-sm-6">
    <?= $form->field($model, 'destination2')->textInput()->label('To') ?>
    </span>
    <span class="col-sm-3">
    <?= $form->field($model, 'destination_2_lat')->textInput()->label('Lat') ?>
        </span>
    <span class="col-sm-3">
    <?= $form->field($model, 'destination_2_lng')->textInput()->label('Lng') ?>
            </span>
    <span class="col-sm-8">
    <?= $form->field($model, 'waypoints')->textInput() ?>
    </span>
    <span class="col-sm-8">
    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>
        </span>

    <?php ActiveForm::end(); ?>

</div>


