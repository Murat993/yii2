<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.06.18
 * Time: 10:52
 */

namespace supervisor\assets;

use yii\web\AssetBundle;
use yii\web\View;


class SVAsset extends AssetBundle
{
    public $sourcePath = '@common/themes/plan/assets';
    public $css = [
        'css/icons/icomoon/styles.css',
        'css/bootstrap.css',
        'css/core.css',
        'css/components.css',
        'css/colors.css',
        'css/custom.css',
        'js/plugins/fancybox/jquery.fancybox.min.css'

    ];
//    public function init()
//    {
//        parent::init();
//        $this->publishOptions['forceCopy'] = true;
//    }

    public $js = [
//        'js/core/libraries/jquery.min.js',
        'js/core/libraries/bootstrap.min.js',
        'js/core/libraries/jquery_ui/widgets.min.js',
        'js/plugins/loaders/pace.min.js',
        'js/plugins/loaders/blockui.min.js',
        'js/plugins/notifications/jgrowl.min.js',
        'js/plugins/ui/moment/moment.min.js',
        'js/plugins/ui/prism.min.js',
        'js/plugins/pickers/daterangepicker.js',
        'js/plugins/pickers/anytime.min.js',
        'js/plugins/pickers/pickadate/picker.js',
        'js/plugins/pickers/pickadate/picker.date.js',
        'js/plugins/pickers/pickadate/picker.time.js',
        'js/plugins/pickers/pickadate/legacy.js',
        'js/plugins/tables/datatables/datatables.min.js',
        'js/plugins/forms/styling/switchery.min.js',
        'js/plugins/forms/selects/select2.min.js',
        'js/plugins/fancybox/jquery.fancybox.min.js',
        'js/plugins/forms/styling/uniform.min.js',
        'js/pages/datatables_basic.js',
        'js/pages/datatables_advanced.js',
        'js/pages/form_inputs.js',
        'js/pages/picker_date.js',
//        'js/pages/form_checkboxes_radios.js',
        'js/core/app.js',
        'js/custom/client.js',
        'js/locales.js',


    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}