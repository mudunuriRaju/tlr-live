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
<script type="application/javascript">
    var toll = <?php echo $Tolls; ?>;

</script>
<div class="toll-boothside-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'UpdateTollSidesForm'
    ]]); ?>

    <?= $form->field($model, 'boothside_from')->textInput(['maxlength' => 45]) ?>
    <?= $form->field($model, 'lat')->textInput(['class' => 'form-control purple_lat'])->label('Lat') ?>

    <?= $form->field($model, 'lng')->textInput(['class' => 'form-control purple_lng'])->label('Lng') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>