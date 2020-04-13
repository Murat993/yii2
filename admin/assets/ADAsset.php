<?php

namespace admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Основные ресурсы административной части
 */
class ADAsset extends AssetBundle
{

    public $sourcePath = '@admin/web/themes/plan/assets';

    public $css = [
        'css/icons/icomoon/styles.css',
        'css/bootstrap.css',
        'css/core.css',
        'css/components.css',
        'css/colors.css',
        'css/colorpicker.css',
        'css/custom.css',
        'js/plugins/fancybox/jquery.fancybox.min.css'


    ];
    public $js = [
//        'js/core/libraries/jquery.min.js',
        'js/core/libraries/jquery_ui/widgets.min.js',
        'js/core/libraries/bootstrap.min.js',

        'js/plugins/notifications/jgrowl.min.js',

        'js/plugins/ui/moment/moment.min.js',
        'js/plugins/ui/prism.min.js',
        'js/plugins/loaders/pace.min.js',
        'js/plugins/loaders/blockui.min.js',

        'js/plugins/pickers/daterangepicker.js',
        'js/plugins/pickers/datepicker.js',
        'js/plugins/pickers/anytime.min.js',
        'js/plugins/pickers/pickadate/picker.js',
        'js/plugins/pickers/pickadate/picker.date.js',
        'js/plugins/pickers/pickadate/picker.time.js',
        'js/plugins/pickers/pickadate/legacy.js',
        'js/plugins/pickers/colorpicker.js',

        'js/plugins/tables/datatables/datatables.min.js',

        'js/plugins/forms/styling/switchery.min.js',
        'js/plugins/forms/selects/select2.min.js',
        'js/plugins/forms/styling/switch.min.js',


        'js/plugins/fancybox/jquery.fancybox.min.js',
        'js/plugins/forms/styling/uniform.min.js',
        'js/pages/datatables_basic.js',
        'js/pages/datatables_advanced.js',
        'js/pages/datatables_custom.js',
        'js/pages/datatables_api.js',
        'js/pages/form_inputs.js',
        'js/pages/picker_date.js',
//        'js/pages/dashboard.js',
//        'js/pages/dashboard_boxed.js',
//        'js/plugins/visualization/d3/d3.min.js',
//        'js/plugins/visualization/d3/d3_tooltip.js',
        'js/locales.js',


        'js/select2.min.js',

        'js/core/libraries/jquery_ui/interactions.min.js',
        'js/core/app.js',

//	    'js/pages/table_elements.js'
        //'js/pages/form_checkboxes_radios.js',
        //'js/pages/form_select2.js'

    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];

}
