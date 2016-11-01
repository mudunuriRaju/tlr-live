<?php

use backend\assets\AppAsset;
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
            'label' => 'Logout (' . Yii::$app->user->identity->email . ')',
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
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/tolls/index'
                    ],
                    [
                        'label' => 'Admin Users',
                        'icon' => 'tags',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/admin-users/index'
                    ],
                    [
                        'label' => 'Toll Groups',
                        'icon' => 'tower',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/groups/index'
                    ],
                    [
                        'label' => 'Vechical Types',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/vechical-types/index'
                    ],
                    [
                        'label' => 'Common Routes',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/common-routes/index'
                    ],
                    [
                        'label' => 'Monthly Cost Type',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/monthlycost/index'
                    ],
                    [
                        'label' => 'Faq',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/settings/index'
                    ],
                    [
                        'label' => 'About Us',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/settings/aboutus'
                    ],
                    [
                        'label' => 'Terms and Conditions',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/settings/termslist'
                    ],
                    [
                        'label' => 'Privacy Policy',
                        'icon' => 'road',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl() . '/settings/privacy'
                    ],
                    /*[
                        'label' => 'Toll User Types',
                        'icon' => 'user',
                        'url' => Yii::$app->getUrlManager()->getBaseUrl().'/master-toll-user/index'
                    ],*/


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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
