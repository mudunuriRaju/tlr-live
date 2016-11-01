<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollBooths */

$this->title = 'Create Toll Booths';
$this->params['breadcrumbs'][] = ['label' => 'Toll Booths', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-booths-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dataTolls' => $dataTolls
    ]) ?>

</div>
