<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterVechicalTypes */

$this->title = 'Vechical Types';
$this->params['breadcrumbs'][] = ['label' => 'Master Vechical Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-vechical-types-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vechical_types_id',
            'type',
            'description',
            'status',
        ],
    ]) ?>
    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>


</div>
