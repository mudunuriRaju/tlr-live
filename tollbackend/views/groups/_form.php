<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use tollbackend\models\Groups;
use tollbackend\models\GroupForm;
use tollbackend\models\GroupSearch;

//use backend\models\


/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'group_key')->textInput(['maxlength' => 45]) ?>


    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
