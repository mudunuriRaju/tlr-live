<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-boothside-form">
    <script type="application/javascript">
        var toll = <?php echo $Tolls; ?>;

    </script>

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'CreateTollSidesForm'
    ]]); ?>

    <span class="col-sm-12"><?= $form->field($model, 'toll_id')->hiddenInput()->label(''); ?><span id="toll_id_side"
                                                                                                   style="color:#a94442"></span></span>

    <span
        class="col-sm-12"><?= $form->field($model, 'boothside_from[]')->textInput(['required' => 'required'])->label('Direction up <img src="http://maps.google.com/mapfiles/ms/icons/purple-dot.png">') ?></span>

    <span
        class="col-sm-4"><?= $form->field($model, 'lat[]')->textInput(['class' => 'form-control purple_lat'])->label('Lat') ?></span>

    <span
        class="col-sm-4"><?= $form->field($model, 'lng[]')->textInput(['class' => 'form-control purple_lng'])->label('Lng') ?></span>

    <span
        class="col-sm-12"><?= $form->field($model, 'boothside_from[]')->textInput(['required' => 'required'])->label('Direction down  <img src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png">') ?></span>

    <span
        class="col-sm-4"><?= $form->field($model, 'lat[]')->textInput(['class' => 'form-control blue_lat'])->label('Lat') ?></span>

    <span
        class="col-sm-4"><?= $form->field($model, 'lng[]')->textInput(['class' => 'form-control blue_lng'])->label('Lng') ?></span>

    <span class="col-sm-8">
       <div class="form-group">
           <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
           <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
       </div>
   </span>

    <?php ActiveForm::end(); ?>
</div>


