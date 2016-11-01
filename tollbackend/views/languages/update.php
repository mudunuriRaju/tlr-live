<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MasterLanguage */

$this->title = 'Update Master Language: ' . ' ' . $model->lagunage_id;
$this->params['breadcrumbs'][] = ['label' => 'Master Languages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lagunage_id, 'url' => ['view', 'id' => $model->lagunage_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="master-language-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
