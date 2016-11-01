<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MasterUserTypes */

$this->title = 'Create Master User Types';
$this->params['breadcrumbs'][] = ['label' => 'Master User Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-user-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
