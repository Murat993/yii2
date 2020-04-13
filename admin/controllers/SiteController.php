<?php

namespace admin\controllers;

use admin\models\UploadForm;
use common\models\Position;
use common\services\UploadManager;
use lav45\translate\models\Lang;
use ruskid\csvimporter\ARImportStrategy;
use ruskid\csvimporter\CSVImporter;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'signup', 'change-language', 'upload'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'settings'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->can('admin-permissions');
                        },
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect(Yii::$app->params['adminDomain'] . 'survey');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->redirect(Yii::$app->params['loginDomain']);
    }

    public function actionChangeLanguage($ln)
    {
        $cookies = Yii::$app->response->cookies;

        if ($cookies->has('lang')) {
            $cookies->remove('lang');
        }

        $cookies->add(new \yii\web\Cookie([
            'name' => 'lang',
            'value' => $ln,
        ]));
        Yii::$app->session->set('lang', $ln);
        Yii::$app->language = $ln;
        return Yii::$app->session->get('lang');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSettings()
    {
        $this->view->title = Yii::t('app', 'Настройки');
        $this->view->params['breadcrumbs'] = [
            $this->view->title
        ];
        $model = Yii::$app->systemSettingsService->getSystemSettings();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                Yii::$app->systemSettingsService->save($model);
                Yii::$app->session->setFlash('success', '');
            } catch (Exception $e) {
                $this->render('error', [$e]);
            }

            return $this->refresh();
        }
        return $this->render('settings', [
            'model' => $model,
        ]);
    }
}
