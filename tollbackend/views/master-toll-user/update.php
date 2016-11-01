<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterTollUserTypes */

$this->title = 'Update Master Toll User Types: ' . ' ' . $model->toll_user_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Master Toll User Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->toll_user_type_id, 'url' => ['view', 'id' => $model->toll_user_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="master-toll-user-types-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
