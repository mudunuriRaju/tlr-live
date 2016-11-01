<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollUsers */

$this->title = 'Create Toll Users';
$this->params['breadcrumbs'][] = ['label' => 'Toll Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataLanguages' => $dataLanguages,
        'dataUsertypes' => $dataUsertypes,
        'dataTolls' => $dataTolls
    ]) ?>

</div>
