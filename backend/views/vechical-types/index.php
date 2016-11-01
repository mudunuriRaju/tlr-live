<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VechicalTypes */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Vechical Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-vechical-types-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Master Vechical Types', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'vechical_types_id',
                'contentOptions' => ['style' => 'width:30px'],
            ],
            [
                'attribute' => 'type',
                'contentOptions' => ['style' => 'width:350px'],
            ],

            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return $data->status == 10 ? 'Active' : 'Inactive';
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
