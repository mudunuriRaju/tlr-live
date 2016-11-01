<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBoothside */

$this->title = 'Update Toll Boothside';
$this->params['breadcrumbs'][] = ['label' => 'Toll Boothsides', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->boothside_id, 'url' => ['view', 'id' => $model->boothside_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="toll-boothside-update toll-boothsides-col-xs-8 col-sm-6">

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
<!--<div class="form-group" style="float:left;">
  <? /*= Html::button('Back',array('name' => 'btnBack','onclick'=>'js:history.go(-1);returnFalse;','class'=>'btn btn-primary')) */ ?>
</div>-->