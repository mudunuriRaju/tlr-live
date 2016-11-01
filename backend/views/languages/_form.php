<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-language-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'laguage_name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'short')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'status')->dropDownList(['10' => 'Active', '00' => 'Deactive']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
