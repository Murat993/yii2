<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.06.18
 * Time: 10:52
 */

namespace client\assets;

use yii\web\AssetBundle;
use yii\web\View;


class ClAsset extends AssetBundle
{
    public $sourcePath = '@client/web/themes/plan/assets';
    public $css = [
        'js/bootstrap/dist/css/bootstrap.css',
        'css/main.css',
        'css/icons/icomoon/styles.css',
        'css/pages/company.css',
//        'css/components.css',
        'css/pages/structure.css',
        'ext/pivottable-master/dist/pivot.css',
//        'ext/subtotal-master/dist/subtotal.min.css',
        'css/colorpicker.css',
//        'css/bootstraps.css',
//        'css/core.css',
//        'css/components.css',
//        'css/colors.css',
//        'css/custom.css',
//        'js/plugins/fancybox/jquery.fancybox.min.css'

    ];

    public $js = [
        'ext/pivottable-master/dist/jquery.ui.touch-punch.min.js',
        'js/bootstrap/dist/js/bootstrap.min.js',
        'js/moment/moment.js',
//        'js/select2.min.js',
        'js/jquery_ui/full.min.js',
        'js/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        'js/daterangepicker.js',
        'js/app.js',
        'js/locales.js',

        'ext/pivottable-master/dist/pivot.js',
        'ext/pivottable-master/dist/pivot.ru.js',
        'ext/pivottable-master/dist/plotly-basic-latest.min.js',
        'ext/pivottable-master/dist/d3.min.js',
        'ext/pivottable-master/dist/export_renderers.js',
        'ext/pivottable-master/dist/plotly_renderers.js',
        'ext/pivottable-master/dist/gchart_renderers.js',
        'ext/pivottable-master/dist/d3_renderers.js',
        'ext/pivottable-master/dist/tips_data.min.js',

        'ext/subtotal-master/dist/subtotal.js',


        'js/datatables.min.js',
        'js/datatables_basic.js',
        'js/datatables_advanced.js',
        'js/colorpicker.js',
//        'js/jquery/dist/jquery.min.js',
//        'js/bootstrap.min.js',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}