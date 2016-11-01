<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollCosts */

$this->title = 'Create Toll Costs';
$this->params['breadcrumbs'][] = ['label' => 'Toll Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-costs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataTolls' => $dataTolls,
        'dataVehicletypes' => $dataVehicletypes,
        'dataMonthlytypes' => $dataMonthlytypes,
        'dataDirection' => $dataDirection,
    ]) ?>

</div>
