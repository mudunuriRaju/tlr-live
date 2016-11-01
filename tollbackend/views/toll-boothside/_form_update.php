<?php
/**
 * Created by PhpStorm.
 * User: kesavam
 * Date: 25/4/15
 * Time: 3:48 PM
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-boothside-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'toll_id')->dropDownList($dataTolls)->label('Toll') ?>

    <?= $form->field($model, 'boothside_towoards')->textInput(['maxlength' => 45]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>