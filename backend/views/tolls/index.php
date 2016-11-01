<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Laguage */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tolls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tolls', ['create'], ['class' => 'btn btn-success']) ?>
        <?= '<div style="color:green;" align="center">' . Yii::$app->session->getFlash('msg') . '</div>'; ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'toll_unique_number',
                'contentOptions' => ['style' => 'width:210px'],
            ],
            [
                'attribute' => 'toll_location',
                'contentOptions' => ['style' => 'width:210px'],
            ],
            [
                'attribute' => 'toll_status',
                'contentOptions' => ['style' => 'width:100px'],
                'value' => function ($data) {
                    return $data->toll_status == 10 ? 'Active' : 'Inactive';
                }

            ],
            [
                'attribute' => 'list',
                'format' => 'html',
                'value' => function ($data) {
                    $count = \backend\models\ReferencePoints::find()->where(['toll_id' => $data->toll_id])->count();
                    if ($count == 0) {
                        $refpoint = Html::a('', ['/reference-points/create?id=' . $data->toll_id], ['class' => 'fa fa-map-marker', 'title' => 'Create Toll Refenece Points']);

                    } elseif ($count > 0 && $count < 6) {
                        $refpoint = Html::a('', ['/reference-points/create?id=' . $data->toll_id], ['class' => 'fa fa-map-marker', 'title' => 'Create Toll Refenece Points']) . '  ' . Html::a('', ['/reference-points/index?id=' . $data->toll_id], ['class' => 'fa fa-map-signs', 'title' => 'Toll Refenece Points']);
                    } else {
                        $refpoint = Html::a('', ['/reference-points/index?id=' . $data->toll_id], ['class' => 'fa fa-map-signs', 'title' => 'Toll Refenece Points']);
                    }
                    return Html::a('', ['/toll-booths/index?id=' . $data->toll_id], ['class' => 'fa fa-list', 'title' => 'tolls Booths']) . "  " . Html::a('', ['/toll-costs/index?id=' . $data->toll_id], ['class' => 'fa fa-money', 'title' => 'Toll Costs']) . "  " . $refpoint;

                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
