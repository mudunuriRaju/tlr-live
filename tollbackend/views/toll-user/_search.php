<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'toll_user_id') ?>

    <?= $form->field($model, 'toll_employee_id') ?>

    <?= $form->field($model, 'toll_password') ?>

    <?= $form->field($model, 'toll_id') ?>

    <?= $form->field($model, 'toll_user_type_id') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
