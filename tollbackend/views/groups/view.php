<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = $model->toll_group_id;
$this->params['breadcrumbs'][] = ['label' => 'Toll Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->toll_group_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->toll_group_id], [
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
            'toll_group_id',
            'group_name',
            'group_key',
        ],
        'id' => 'toll-dview',
    ]) ?>

</div>


