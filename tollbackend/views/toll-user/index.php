<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel tollbackend\models\TollUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Toll Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Toll Users', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'toll_employee_id', 'filterInputOptions' => ['placeholder' => 'Toll Employee Id', 'class' => "form-control"]],
            ['attribute' => 'toll.toll_unique_number', 'filterInputOptions' => ['placeholder' => 'Toll Name', 'class' => "form-control"]],
            ['attribute' => 'tollUserType.type_name', 'filterInputOptions' => ['placeholder' => 'Toll User Type', 'class' => "form-control"]],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return $data->status == 10 ? 'Active' : 'Inactive';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'headerOptions' => ['width' => '80'],
                'template' => (Yii::$app->user->identity->toll_user_type_id == 3 || Yii::$app->user->identity->toll_user_type_id == 2) ? '{view} {update}{link}' : '{view} {update}{delete}{link}',
            ],
        ],
    ]);
    ?>

</div>
