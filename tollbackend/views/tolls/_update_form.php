<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*use yii\helpers\ArrayHelper;
use backend\models\MasterLanguage;
use \backend\models\MasterTollUserTypes;*/
//use backend\models\


/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'toll_unique_number')->textInput(['maxlength' => 45]) ?>
    <?= $form->field($model, 'toll_location')->textInput(['maxlength' => 256]) ?>
    <?= $form->field($model, 'toll_lat')->textInput() ?>
    <?= $form->field($model, 'toll_lng')->textInput() ?>
    <?= $form->field($model, 'toll_radius')->textInput() ?>
    <?= $form->field($model, 'allowed_ip')->textInput() ?>
    <?= $form->field($model, 'motorway_id')->textInput() ?>
    <?= $form->field($model, 'toll_status')->dropDownList(['10' => 'Active', '00' => 'De-active']) ?>

    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
