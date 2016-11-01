<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tollbackend\models\TollUsers */

$this->title = 'View Toll User';
$this->params['breadcrumbs'][] = ['label' => 'Toll Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toll-users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'toll_user_id',
            'toll_employee_id',
            'toll_password',
            'toll_id',
            'tollUserType.type_name',
            [
                'attribute' => 'status',
                'value' => $model->status == 10 ? 'Active' : 'Inactive'
            ],
            'group_id',
            'language.laguage_name',
        ],
    ]) ?>

</div>

<div class="form-group">
    <?= Html::button('Back', array('name' => 'btnBack', 'onclick' => 'js:history.go(-1);returnFalse;', 'class' => 'btn btn-primary')) ?>
</div>
