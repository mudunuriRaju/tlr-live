<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'View Faq';
$this->params['breadcrumbs'][] = ['label' => 'Faq', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'column1',
            'column2',

        ],
        'id' => 'toll-dview',
    ]) ?>

    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>

</div>


