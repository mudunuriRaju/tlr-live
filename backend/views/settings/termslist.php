<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Laguage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Terms and Conditions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Terms and conditions', ['termsadd'], ['class' => 'btn btn-success']) ?>
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
                'header' => 'Title',
                'filterInputOptions' => ['placeholder' => 'Heading', 'class' => "form-control"]
            ],

            [
                'attribute' => 'column2',
                'header' => 'Terms and Conditions',
                'contentOptions' => ['style' => 'width:100px'],
                'filterInputOptions' => ['placeholder' => 'Terms and Conditions', 'class' => "form-control"]
            ],

        ],
    ]);
    ?>

</div>
