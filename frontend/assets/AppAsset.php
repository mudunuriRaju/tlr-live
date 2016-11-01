<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
        'css/bootstrap-3.3.6-dist/css/bootstrap.min.css',
        'css/bootstrap-3.3.6-dist/css/bootstrap-theme.min.css',
        'css/Font-Awesome-master/css/font-awesome.min.css',
        'css/custom.css',
    ];
    public $js = [
        'https://code.jquery.com/jquery-2.1.4.min.js',
        'js/angular/angular.min.js',
        'js/angular/angular-animate.min.js',
        'js/ui-bootstrap-tpls-1.0.3.min.js',
        'js/controllers/HomeCtrl.js',
        //'js/controllers/HistoryCtrl.js',
        //'js/controllers/FavoriteCtrl.js',
        //'js/controllers/VehicalsCtrl.js',
        //'js/directives/TollTabsDirective.js',
        //'js/directives/TollPanesDirective.js',
        //'js/services/vehiclesService.js',
        //'js/services/HistoryService.js'
    ];
    public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
