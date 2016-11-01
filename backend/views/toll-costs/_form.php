<script type="text/javascript">
    function changenew(v) {
        document.getElementById("monthtrips").style.visibility = 'hidden';
        if (v == 4 || v == 5 || v == 6) {
            document.getElementById("monthtrips").style.visibility = 'visible';
        }

    }
</script>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollCosts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toll-costs-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'toll_id')->hiddenInput()->label('') ?>
    <?= $form->field($model, 'direction_id')->dropDownList($dataDirection)->label('Direction') ?>
    <?= $form->field($model, 'vechical_types_id')->textInput()->dropDownList($dataVehicletypes)->label('Vechical Types ID') ?>
    <?= $form->field($model, 'single_trip_cost')->textInput() ?>
    <?= $form->field($model, 'round_trip_cost')->textInput() ?>
    <?= $form->field($model, 'monthly_cost')->textInput() ?>
    <?= $form->field($model, 'monthly_type_id')->textInput()->dropDownList($dataMonthlytypes, ['id' => 'monthtypecount', 'onchange' => 'changenew(this.value)']) ?>
    <?php echo '<div id="monthtrips" style="visibility:hidden;">' ?>
    <?= $form->field($model, 'number_monthly_trips')->textInput() ?>
    <?php echo '</div>'; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
