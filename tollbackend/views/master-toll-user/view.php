<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterTollUserTypes */

$this->title = $model->toll_user_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Master Toll User Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-toll-user-types-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->toll_user_type_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->toll_user_type_id], [
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
            'toll_user_type_id',
            'type_name',
            'prioity',
        ],
    ]) ?>

</div>
