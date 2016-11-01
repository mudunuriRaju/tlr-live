<?php

use tollbackend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use kartik\sidenav\SideNav;

//use kartik\sidenav\SideNav;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
//echo Yii::$app->controller->id;
if (Yii::$app->controller->id != 'site') {
    ?>
    <div class="wrap row">
        <?php
        NavBar::begin([
            'brandLabel' => 'My Company',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        $menuItems = [
            ['label' => 'Home', 'url' => ['/site/index']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
        } else {
            $menuItems[] = [
                'label' => 'Logout (' . Yii::$app->user->identity->toll_employee_id . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <div class="container col-sm-3">
                <?php
                echo SideNav::widget([
                    'type' => SideNav::TYPE_DEFAULT,
                    'heading' => 'Menu',
                    'items' => [
                        [
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/',
                            'label' => 'Home',
                            'icon' => 'home'
                        ],
                        [
                            'label' => 'Tolls',
                            'icon' => 'tags',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/tolls/index',
                            'visible' => (Yii::$app->user->identity->toll_user_type_id == 1) ? true : false,
                        ],
                        [
                            'label' => 'Toll Users',
                            'icon' => 'user',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/toll-user/index'
                        ],
                        [
                            'label' => 'Toll Booths',
                            'icon' => 'tower',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/toll-booths/index'
                        ],
                        [
                            'label' => 'Toll Booth between',
                            'icon' => 'road',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/toll-boothside/index',
                            'visible' => (Yii::$app->user->identity->toll_user_type_id == 1 || Yii::$app->user->identity->toll_user_type_id == 2) ? true : false,
                        ],
                        [
                            'label' => 'Toll Cost',
                            'icon' => 'euro',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/toll-costs/index',
                            'visible' => (Yii::$app->user->identity->toll_user_type_id == 1 || Yii::$app->user->identity->toll_user_type_id == 2) ? true : false,
                        ],
                        [
                            'label' => 'Attach Group',
                            'icon' => 'user',
                            'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/groups/create',
                            'visible' => (Yii::$app->user->identity->toll_user_type_id == 1 && (Yii::$app->user->identity->group_id == "NULL" || Yii::$app->user->identity->group_id == 0)) ? true : false,
                        ],
                    ],
                ]);
                ?>

            </div>
        <?php } ?>
        <div class="container <?php if (!Yii::$app->user->isGuest) { ?>col-sm-9<?php } ?>">
            <?=
            Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])
            ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Omulus Infotech <?= date('Y') ?></p>
            <p class="pull-right"><!--<?= Yii::powered() ?>--></p>
        </div>
    </footer>
<?php } else {
    echo $content;
} ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
