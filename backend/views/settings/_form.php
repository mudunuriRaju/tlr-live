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

    <?= $form->field($model, 'column1')->textInput() ?>
    <?= $form->field($model, 'column2')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


