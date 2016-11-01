<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'View CommonRoutes';
$this->params['breadcrumbs'][] = ['label' => 'CommonRoutes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Common-routes-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'destination1',
            'destination2',
            'waypoints',
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
