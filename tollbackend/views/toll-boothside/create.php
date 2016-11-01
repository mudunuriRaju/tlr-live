<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */

$this->title = 'Create Toll Boothside';
$this->params['breadcrumbs'][] = ['label' => 'Toll Boothsides', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-boothside-create toll-boothsides-col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataTolls' => $dataTolls,
        'Tolls' => $Tolls
    ]) ?>

</div>
<div class="master-language-create col-xs-8 col-sm-6" style="height: 500px; padding-top: 100px">
    <style> #map-canvas {
            height: 100%
        }</style>

    <div id="map-canvas" style="display: block"></div>
</div>
