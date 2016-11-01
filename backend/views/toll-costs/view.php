<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollCosts */

$this->title = 'View Toll Cost';
$this->params['breadcrumbs'][] = ['label' => 'Toll Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-costs-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'toll_cost_id',
            'toll.toll_unique_number',
            'masterVechicalTypes.type',
            'single_trip_cost',
            'round_trip_cost',
            'monthly_cost',
            'created_on',
        ],
    ]) ?>

</div>

<div class="form-group">
    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
</div>
