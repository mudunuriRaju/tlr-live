<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

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
/* NavBar::begin([
  'brandLabel' => 'My Company',
   'brandUrl' => Yii::$app->homeUrl,
    'options' => [
    'class' => 'navbar-inverse navbar-fixed-top',
    ],
  ]);
     $menuItems = [
         ['label' => 'Home', 'url' => ['/site/index']],
         ['label' => 'About', 'url' => ['/site/about']],
         ['label' => 'Contact', 'url' => ['/site/contact']],
         ['label' => 'Faq', 'url' => ['/settings/index']],
         ['label' => 'Aboutus', 'url' => ['/settings/aboutus']],
         ['label' => 'Privacy', 'url' => ['/settings/privacy']],
         ['label' => 'Terms', 'url' => ['/settings/termslist']],
     ];
     if (Yii::$app->user->isGuest) {
         $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
         $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
     } else {
         $menuItems[] = [
             'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
             'url' => ['/site/logout'],
             'linkOptions' => ['data-method' => 'post']
         ];
     }
     echo Nav::widget([
         'options' => ['class' => 'navbar-nav navbar-right'],
         'items' => $menuItems,
     ]);
     NavBar::end();*/
?>


<?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
]) ?>
<?= Alert::widget() ?>
<?= $content ?>


<!--<footer class="footer">
    <div class="container">
      <p class="pull-right">&copy; Omulusinfotech.com 2015</p>
    </div>
</footer>
-->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
