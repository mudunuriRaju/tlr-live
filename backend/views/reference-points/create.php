<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ReferencePoints */

$this->title = 'Create Reference Points';
$this->params['breadcrumbs'][] = ['label' => 'Reference Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-points-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'count' => $count,
    ]) ?>

</div>
