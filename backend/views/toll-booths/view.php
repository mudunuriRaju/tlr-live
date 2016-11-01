<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBooths */

$this->title = 'View Tollbooth';
$this->params['breadcrumbs'][] = ['label' => 'Toll Booths', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-booths-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'booth_id',
            'toll.toll_unique_number',
            [
                'attribute' => 'booth_unique_id',
                'value' => $model->booth_unique_id = 'Booth ' . $model->booth_unique_id
            ],
            'created_on',
        ],
    ]) ?>

</div>
<div class="form-group">
    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
</div>
