<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ReferencePoints */

$this->title = 'Update Reference Points: ' . ' ' . $model->toll_ref_point_id;
$this->params['breadcrumbs'][] = ['label' => 'Reference Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->toll_ref_point_id, 'url' => ['view', 'id' => $model->toll_ref_point_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reference-points-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
