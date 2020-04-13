<?php

namespace admin\controllers;

use common\models\User;
use lav45\translate\models\Lang;
use yii\web\Controller;
use Yii;

class BaseController extends Controller
{

    private $_setting;

    public function init()
    {
        parent::init();
        Yii::$app->language = Yii::$app->session->get('lang') ? Yii::$app->session->get('lang') : 'ru';
        Yii::$container->set('common\translate\TranslatedBehavior', [
            'language' => isset($_GET['lang_id']) ? $_GET['lang_id'] : Yii::$app->session->get('lang')
        ]);

        if (!Yii::$app->session->get('lang')) {
            $cookies = Yii::$app->response->cookies;
            $ln = $cookies->getValue('lang', 'ru-RU');
            Yii::$app->session->set('lang', $ln);
            Yii::$app->language = $ln;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'lang',
                'value' => $ln,
            ]));
        }
        $this->_setting = Yii::$app->systemSettingsService->getSystemSettings();
        $this->setAppParams();
        if (Yii::$app->user->can('admin-permissions') &&
            Yii::$app->user->identity->role === User::ROLE_CLIENT_SUPER) {
            $adminId = Yii::$app->user->getId();
            Yii::$app->userService->deleteClientAdmin($adminId);
            return $this->goHome();
        }
    }

    protected function setAppParams()
    {
        if (
        empty($this->_setting || !isset($this->_setting->attributes) || empty($this->_setting->attributes)
        )) {
            return false;
        }

        foreach ($this->_setting->attributes as $key => $attr) {
            Yii::$app->params[$key] = $attr;
        }
    }

}
