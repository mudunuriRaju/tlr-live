<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollUsers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-users-form">
    <?php
    $readonly_fields = Yii::$app->user->identity->toll_user_type_id == 1 ? false : ($model->isNewRecord ? false : true);
    ?>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'toll_employee_id')->textInput(['maxlength' => 45, 'readOnly' => $readonly_fields]); ?>

    <?= $form->field($model, 'toll_password')->passwordInput(['minlength' => 8, 'readOnly' => $readonly_fields]) ?>

    <?= $form->field($model, 'toll_id')->dropDownList($dataTolls, array('readOnly' => $readonly_fields))->label('Toll') ?>

    <?= $form->field($model, 'toll_user_type_id')->dropDownList($dataUsertypes, array('readOnly' => $readonly_fields))->label('User type') ?>

    <?= $form->field($model, 'status')->dropDownList(['10' => 'Active', '00' => 'De-active']) ?>

    <?= $form->field($model, 'language_id')->dropDownList($dataLanguages)->label('Language') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
