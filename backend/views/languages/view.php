<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = $model->lagunage_id;
$this->params['breadcrumbs'][] = ['label' => 'Master Languages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lagunage_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lagunage_id], [
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
            'lagunage_id',
            'laguage_name',
            'short',
            'status',
        ],
    ]) ?>

</div>
