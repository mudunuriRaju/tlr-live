<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Laguage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monthly Cost Type';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Type', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?=
    GridView::widget([

        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'type_name', 'filterInputOptions' => ['placeholder' => 'Type Name', 'class' => "form-control"]],

            [
                'attribute' => 'type_description',
                'filterInputOptions' => ['placeholder' => 'Type description', 'class' => "form-control"]
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return $data->status == 10 ? 'Active' : 'Inactive';
                },
                'filterInputOptions' => ['placeholder' => 'Status', 'class' => "form-control"]

            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
