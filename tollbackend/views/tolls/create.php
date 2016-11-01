<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'Create Toll';
$this->params['breadcrumbs'][] = ['label' => 'Tolls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-create col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model
    ])
    ?>

</div>
<div class="master-language-create col-xs-8 col-sm-6" style="height: 500px; padding-top: 100px">
    <style> #map-canvas {
            height: 100%
        }</style>

    <div id="map-canvas" style="display: block"></div>
</div>
