<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Laguage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Faq';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Faq', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'column1',
                'header' => 'Question',
                'filterInputOptions' => ['placeholder' => 'Question', 'class' => "form-control"]],

            [
                'attribute' => 'column2',
                'header' => 'Answer',
                'contentOptions' => ['style' => 'width:550px'],
                'filterInputOptions' => ['placeholder' => 'Answer', 'class' => "form-control"]
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'headerOptions' => ['width' => '80'],
                'template' => '{update} {delete}{link}',
            ],
        ],
    ]);
    ?>
</div>
