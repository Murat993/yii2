<?php

namespace client\controllers;

use client\models\RequestPasswordResetForm;
use client\models\UploadForm;
use common\models\User;
use common\services\UploadManager;
use lav45\translate\models\Lang;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use client\models\LoginForm;

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
                        'actions' => ['login', 'error', 'signup', 'reset-password-request', 'change-language', 'switch-user'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'upload'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if ($action->id == 'upload') {
                                if (Yii::$app->user->can('client-user-permissions') && Yii::$app->userService->checkClientEdit()) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                            $userRole = Yii::$app->user->identity->role;
                            if (Yii::$app->user->can('client-user-permissions') && ($userRole === User::ROLE_CLIENT_USER || $userRole === User::ROLE_CLIENT_SUPER)) {
                                return true;
                            } else {
                                return false;
                            }
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
        $surveyQuery = (new Query())->select([
            "sl.name, s.survey_from, s.survey_to, (select count(*) from survey_filial sf where sf.survey_id = s.id) as objects_count,
            getSurveysCountByStatus(555,s.id) as complete_count, getSurveysCountByStatus(-1,s.id) as all_count,
            (getSurveysCountByStatus(555,s.id)*100/getSurveysCountByStatus(-1,s.id)) as percent_complete
            from survey s, survey_lang sl
            where s.id = sl.survey_id
            and sl.lang_id = 'ru'
            and s.client_id = {$this->getClientId()}"
        ]);

        $lastComments = Yii::$app->reportService->lastComments($this->getClientId());
        $commentsDataProvider = new ArrayDataProvider([
            'allModels' => $lastComments->queryAll(),
            'pagination' => false
        ]);

        $lastSurveys = Yii::$app->reportService->surveys($this->getClientId());
        $surveysDataProvider = new ArrayDataProvider([
            'allModels' => $lastSurveys->all(),
            'pagination' => false,
            'sort' => false
        ]);

        $colorMap = Yii::$app->userService->getClientColorMap();

        return $this->render('index', [
            'activeSurveys' => $surveyQuery->all(),
            'commentsDataProvider' => $commentsDataProvider,
            'surveysDataProvider' => $surveysDataProvider,
            'colorMap' => $colorMap
        ]);
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

    public function actionResetPasswordRequest()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new RequestPasswordResetForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                Yii::$app->userService->resetPassword($model->user);
                $this->redirect('login');
            } catch (Exception $e) {
                //flash message
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model
        ]);
    }

    public function actionUpload($type)
    {
        $model = new UploadForm();
        switch ($type) {
            case 'structure':
                $type = 4;
                $this->view->title = Yii::t('app', 'Загрузка CSV - Структура объектов');
                break;
            case 'object':
                $type = 3;
                $this->view->title = Yii::t('app', 'Загрузка CSV - Объекты');
                break;
            case 'employee':
                $type = 2;
                $this->view->title = Yii::t('app', 'Загрузка CSV - Сотрудники');
                break;
            default:
                $type = null;
                break;
        }
        if ($type) {
            $model->type = $type;
            $model->client_id = $this->getClientId();
            if ($model->load(Yii::$app->request->post())) {
                $manager = new UploadManager($model);
                if ($result = $manager->saveAll()) {
                    Yii::$app->session->setFlash('success', 'Успешно обработано: ' .
                        count($result['success']) . ', ' . 'Неудачно: ' . count($result['error']));
                    $this->redirect('upload');
                }
            }

            $this->view->params['breadcrumbs'] = [
                $this->view->title
            ];
            return $this->render('upload', [
                'model' => $model,
            ]);
        } else {
            Yii::$app->session->setFlash('error', 'Неправильный запрос');
            $this->redirect('index');
        }
    }

    public function actionSwitchUser()
    {
        $originalId = Yii::$app->session->get('user.idbeforeswitch');
        if ($originalId) {
            $userModel = User::findOne($originalId);
            $duration = 0;
            Yii::$app->user->switchIdentity($userModel, $duration);
            Yii::$app->session->remove('user.idbeforeswitch');
        }
        $this->redirect(Yii::$app->params['adminDomain']);
    }

}
