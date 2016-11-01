<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-boothside-form">
    <script type="application/javascript">
        var tolls = <?php echo $Tolls; ?>;

    </script>

    <?php $form = ActiveForm::begin(); ?>

    <span
        class="col-sm-12"><?= $form->field($model, 'toll_id')->dropDownList($dataTolls, ['prompt' => '-Choose a Category-', 'onchange' => '$.post( "' . Yii::$app->urlManager->createUrl('toll-boothside/boothsidecount?id=') . '"+$(this).val(), function( data ) {
                if(data >= 2){
                    $(".field-tollboothside-toll_id").removeClass("has-success");
                    $(".field-tollboothside-toll_id").addClass("has-error");
                    $("#toll_id_side").html("This toll has already two booth sides");
                }
                //$(".field-tollboothside-toll_id").

           });'])->label('Toll') ?><span id="toll_id_side" style="color:#a94442"></span></span>

    <span class="col-sm-12"><?= $form->field($model, 'boothside_from')->textInput()->label('From') ?></span>

    <span class="col-sm-4"><?= $form->field($model, 'lat')->textInput()->label('Lat') ?></span>

    <span class="col-sm-4"><?= $form->field($model, 'lng')->textInput()->label('Lng') ?></span>

    <span class="col-sm-8">
       <div class="form-group">
           <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
           <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
       </div>
   </span>

    <?php ActiveForm::end(); ?>
</div>


