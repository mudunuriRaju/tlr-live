<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ReferencePoints */

$this->title = $model->toll_ref_point_id;
$this->params['breadcrumbs'][] = ['label' => 'Reference Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-points-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->toll_ref_point_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->toll_ref_point_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'toll_id',
            'toll_axis',
            'lat',
            'lng',
            'toll_ref_point_id',
        ],
    ]) ?>

</div>
