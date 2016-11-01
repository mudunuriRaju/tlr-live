<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterVechicalTypes */

$this->title = 'Update Master Vechical Types: ';
$this->params['breadcrumbs'][] = ['label' => 'Master Vechical Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vechical_types_id, 'url' => ['view', 'id' => $model->vechical_types_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="master-vechical-types-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
