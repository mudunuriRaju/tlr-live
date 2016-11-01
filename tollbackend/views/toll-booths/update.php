<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBooths */

$this->title = 'Update Toll Booths: ' . ' ' . $model->booth_id;
$this->params['breadcrumbs'][] = ['label' => 'Toll Booths', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->booth_id, 'url' => ['view', 'id' => $model->booth_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="toll-booths-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataTolls' => $dataTolls
    ]) ?>

</div>
