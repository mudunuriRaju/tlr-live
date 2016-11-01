<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AdminUsers */

$this->title = 'Update Admin Users: ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->admin_user_id, 'url' => ['view', 'id' => $model->admin_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="admin-users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
