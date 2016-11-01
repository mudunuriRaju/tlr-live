<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel tollbackend\models\TollBoothsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Toll Booths';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-booths-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //if(Yii::$app->user->identity->toll_user_type_id == 1) { ?><?php //Html::a('Create Toll Booths', ['create'], ['class' => 'btn btn-success']); ?><?php //} ?>
		 <?= '<div style="color:green;" align="center">'.Yii::$app->session->getFlash('msg').'</div>'; ?>
    </p>

    <?= GridView::widget(['dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [['class' => 'yii\grid\SerialColumn'],
    ['attribute' => 'toll.toll_unique_number', 'filterInputOptions' => ['placeholder' => 'Toll Name', 'class' => "form-control"]],
    ['attribute' => 'boothside.boothside_from', 'filterInputOptions' => ['placeholder' => 'Boothside Towards', 'class' => "form-control"]],
    ['attribute' => 'booth_unique_id',
    'filterInputOptions' => ['placeholder' => 'Booth Name', 'class' => "form-control"],
    'value' => function($data){
    return $data->booth_unique_id = 'Booth '. $data->booth_unique_id;
    }],
    ['class' => 'yii\grid\ActionColumn',
    'header' => 'Action',
    'headerOptions' => ['width' => '80'],
    'template' => (Yii::$app->user->identity->type == 3 || Yii::$app->user->identity->type == 2) ? '{view}{link}' :'{view} {delete}{link}',],],]); ?>

</div>
