<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBooths */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-booths-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'toll_id')->dropDownList($dataTolls, ['prompt' => '-Choose a Category-', 'onchange' => '$.post( "' . Yii::$app->urlManager->createUrl('toll-booths/boothside?id=') . '"+$(this).val(), function( data ) {
                $( "select#tollbooths-booth_side" ).html( data );
           });
      '])->label('Toll') ?>

    <?= $form->field($model, 'booth_side')->dropDownList($dataSides)->label('Booth Side') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
