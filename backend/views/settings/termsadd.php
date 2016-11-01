<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Terms and Conditions';
$this->params['breadcrumbs'][] = ['label' => 'Terms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-create col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>
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
</div>


