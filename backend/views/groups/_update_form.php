<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\MasterLanguage;
use \backend\models\Groups;
use \backend\models\GroupSearch;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="toll-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'group_name')->textInput(['readonly' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Re-Generate', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
