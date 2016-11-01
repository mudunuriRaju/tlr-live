<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'Update Monthly Cost Type: ';
$this->params['breadcrumbs'][] = ['label' => 'Monthly Cost Type', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->monthly_type_id, 'url' => ['view', 'id' => $model->monthly_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="master-language-update col-xs-8 col-sm-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_update_form', [
        'model' => $model,
    ]) ?>

</div>
