<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel tollbackend\models\TollBoothsideSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Toll Boothsides';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-boothside-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <p>
        <?php if (Yii::$app->user->identity->toll_user_type_id == 1) { ?><?= Html::a('Create Toll Boothside', ['create'], ['class' => 'btn btn-success']) ?><?php } ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'toll.toll_unique_number', 'filterInputOptions' => ['placeholder' => 'Toll Name', 'class' => "form-control"]],
            ['attribute' => 'boothside_from', 'filterInputOptions' => ['placeholder' => 'Boothside Towards', 'class' => "form-control"]],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'headerOptions' => ['width' => '80'],
                'template' => Yii::$app->user->identity->toll_user_type_id == 2 ? '{view}{link}' : '{view} {update} {delete}{link}',
            ],
        ],
    ]); ?>

</div>
