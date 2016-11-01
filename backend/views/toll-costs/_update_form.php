<script type="text/javascript">
    function changenew(v) {
        if (v > 3) {
            document.getElementById("monthtrips").style.visibility = 'visible';
        }
        else {
            document.getElementById("monthtrips").style.visibility = 'hidden';
        }

    }
</script>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use tollbackend\models\TollList;
use tollbackend\models\MasterVechicalTypes;

//use backend\models\

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">


    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'toll_id')->textInput()->dropDownList($dataTolls)->label('Toll ID') ?>
    <?= $form->field($model, 'direction_id')->dropDownList($dataDirection)->label('Direction') ?>
    <?= $form->field($model, 'vechical_types_id')->textInput()->dropDownList($dataVehicletypes)->label('Vechical Types ID') ?>
    <?= $form->field($model, 'single_trip_cost')->textInput() ?>
    <?= $form->field($model, 'round_trip_cost')->textInput() ?>
    <?= $form->field($model, 'monthly_cost')->textInput() ?>
    <?= $form->field($model, 'monthly_type_id')->textInput()->dropDownList($dataMonthlytypes, ['id' => 'monthtypecount', 'onchange' => 'changenew(this.value)'])->label('Monthly Type ID') ?>

    <?php echo '<div id="monthtrips" style="visibility:hidden;">' ?>
    <?= $form->field($model, 'number_monthly_trips')->textInput() ?>
    <?php echo '</div>'; ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
