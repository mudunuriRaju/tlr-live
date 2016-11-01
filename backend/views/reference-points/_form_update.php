<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ReferencePoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reference-points-form">

    <?php $form = ActiveForm::begin(); ?>

    <span class="col-sm-12">
        <span class="col-sm-5">
            <?= $form->field($model, 'toll_axis')->textInput(['maxlength' => 45]) ?>
        </span>
        <span class="col-sm-5">
            <?= $form->field($model, 'direction_id')->dropDownList(\yii\helpers\ArrayHelper::map(\api\models\TollBoothside::find()->where(['toll_id' => $model->toll_id])->asArray(true)->all(), 'boothside_id', 'boothside_from')) ?>
        </span>
        <span class="col-sm-5">
            <?= $form->field($model, 'lat')->textInput(['maxlength' => 45]) ?>
        </span>
        <span class="col-sm-5">
            <?= $form->field($model, 'lng')->textInput(['maxlength' => 45]) ?>
        </span>
        <span class="col-sm-4">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        </span>
    </span>


    <?php ActiveForm::end(); ?>

</div>
