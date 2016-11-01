<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'View Monthlycost';
$this->params['breadcrumbs'][] = ['label' => 'Monthlycost', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'type_name',
            'type_description',
            'status',
        ],
        'id' => 'toll-dview',
    ]) ?>

    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>

</div>
