<?php

namespace client\assets;

use yii\web\AssetBundle;
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.06.18
 * Time: 10:33
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        'css/colors.css',
        'css/components.css',
        'css/core.css',
        'css/bootstrap.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
