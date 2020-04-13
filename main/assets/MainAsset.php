<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 19.07.18
 * Time: 12:26
 */

namespace main\assets;


use yii\web\AssetBundle;
use yii\web\View;

class MainAsset extends AssetBundle
{
    public $sourcePath = 'themes/plan/assets';
    public $css = [
        'css/icons/icomoon/styles.css',
        'css/bootstrap.css',
        'css/core.css',
        'css/components.css',
        'css/colors.css',
        'css/custom.css'

    ];
    public $js = [
        //'js/core/libraries/jquery.min.js',
        'js/plugins/loaders/pace.min.js',
        'js/core/libraries/bootstrap.min.js',
        'js/plugins/loaders/blockui.min.js',

        'js/core/app.js',

    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}