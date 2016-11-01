<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'View Toll';
$this->params['breadcrumbs'][] = ['label' => 'Tolls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'toll_id',
            'toll_unique_number',
            'toll_location',
            'toll_lat',
            'toll_lng',
            'toll_radius',
            'motorway_id',
            'amount',
            'allowed_ip',
            'group_id',
        ],
        'id' => 'toll-dview',
    ]) ?>

    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>

</div>


<div class="toll-create col-xs-8 col-sm-6" style="height: 500px; padding-top: 100px">
    <style> #map-canvas {
            height: 100%
        }</style>

    <div id="map-canvas" style="display: block"></div>
</div>
