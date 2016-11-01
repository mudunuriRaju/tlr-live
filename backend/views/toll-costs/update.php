<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollCosts */

$this->title = 'Update Toll Costs: ' . ' ' . $model->toll_cost_id;
$this->params['breadcrumbs'][] = ['label' => 'Toll Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->toll_cost_id, 'url' => ['view', 'id' => $model->toll_cost_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="toll-costs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_update_form', [
        'model' => $model,
        'dataTolls' => $dataTolls,
        'dataVehicletypes' => $dataVehicletypes,
        'dataMonthlytypes' => $dataMonthlytypes,
        'dataDirection' => $dataDirection
    ]) ?>

</div>
