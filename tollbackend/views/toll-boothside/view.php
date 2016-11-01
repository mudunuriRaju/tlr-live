<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */

$this->title = 'Toll Boothsides';
$this->params['breadcrumbs'][] = ['label' => 'Toll Boothsides', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-boothside-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'boothside_id',
            'boothside_towoards',
            'toll.toll_unique_number',
            'created_on',
        ],
    ]) ?>

</div>
<div class="form-group">
    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
</div>

