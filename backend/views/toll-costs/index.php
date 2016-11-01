<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel tollbackend\models\TollCostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Toll Costs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-costs-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Toll Costs', ['create?id=' . $id], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'toll.toll_unique_number', 'filterInputOptions' => ['placeholder' => 'Toll Name', 'class' => "form-control"]],
            ['attribute' => 'masterVechicalTypes.type', 'filterInputOptions' => ['placeholder' => 'Vehicle Type', 'class' => "form-control"]],
            'single_trip_cost',
            'round_trip_cost',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'headerOptions' => ['width' => '80'],
                'template' => Yii::$app->user->identity->type == 2 ? '{view}{link}' : '{view} {update} {delete}{link}',
            ],
        ],
    ]); ?>

</div>
