<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Laguage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Common Routes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Routes', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'destination1', 'filterInputOptions' => ['placeholder' => 'From Location', 'class' => "form-control"]],

            [
                'attribute' => 'destination2',
                'filterInputOptions' => ['placeholder' => 'To Location', 'class' => "form-control"]
            ],
            [
                'attribute' => 'waypoints',
                'filterInputOptions' => ['placeholder' => 'Waypoints', 'class' => "form-control"]

            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
