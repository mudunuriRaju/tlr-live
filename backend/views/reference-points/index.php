<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ReferencePointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reference Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-points-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a('Create Reference Points', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'boothside.boothside_from', 'filterInputOptions' => ['placeholder' => 'Direction', 'class' => "form-control"]],
            'toll_axis',
            'lat',
            'lng',
            //'toll_ref_point_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
