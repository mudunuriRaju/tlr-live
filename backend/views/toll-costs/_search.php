<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollCostsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-costs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'toll_cost_id') ?>

    <?= $form->field($model, 'toll_id') ?>

    <?= $form->field($model, 'vechical_types_id') ?>

    <?= $form->field($model, 'single_trip_cost') ?>

    <?= $form->field($model, 'round_trip_cost') ?>

    <?php // echo $form->field($model, 'created_on') ?>

    <?php // echo $form->field($model, 'updated_on') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
