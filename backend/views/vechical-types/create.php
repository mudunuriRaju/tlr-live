<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MasterVechicalTypes */

$this->title = 'Create Master Vechical Types';
$this->params['breadcrumbs'][] = ['label' => 'Master Vechical Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-vechical-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
