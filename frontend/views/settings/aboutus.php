<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Toller - AboutUs';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div>
                <h2 class="bg_faq" style="margin-top:50px;">About US</h2>
                <div class="bg_faq1">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            [
                                'attribute' => 'column2',
                                'header' => 'Description',
                                'contentOptions' => ['style' => 'font-family:Arial;line-height:25px;'],

                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
