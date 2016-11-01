<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterTollUserTypes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-toll-user-types-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'toll_user_type_id')->textInput() ?>

    <?= $form->field($model, 'type_name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'prioity')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
