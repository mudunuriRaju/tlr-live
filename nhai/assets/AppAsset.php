<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nhai\assets;

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
        'css/site.css',
        'css/style.css',
    ];
    public $js = [
        //'js/jquery.min.js',
        'https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDeWpP7Y-UOu810O0MLLlIMXHceUEfUQN4',
        'js/angular-1.5.5/angular.min.js',
        'js/angular-1.5.5/angular-animate.min.js',
        'js/angular-1.5.5/angular-cookies.min.js',
        'js/angular-1.5.5/angular-route.min.js',
        'js/RouteBoxer.js',
        'js/c3.js',
        'js/scripts.js',
        'js/scrolltopcontrol.js',
        'js/gMap.js',
        'js/app.js',
        'js/ConsessCtrl.js',
        'js/autocomplete.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
