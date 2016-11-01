<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'View Toll Group';
$this->params['breadcrumbs'][] = ['label' => 'Toll Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-view col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'toll_group_id',
            'group_name',
            'group_key',
        ],
        'id' => 'toll-dview',
    ]) ?>

    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
</div>


