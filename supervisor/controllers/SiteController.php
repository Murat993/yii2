<?php

namespace supervisor\controllers;

use supervisor\models\RequestPasswordResetForm;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use supervisor\models\LoginForm;

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
                        'actions' => ['login', 'error', 'signup', 'reset-password-request', 'change-language'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->can('supervisor-permissions') && (Yii::$app->user->identity->role === User::ROLE_SUPERVISOR || Yii::$app->user->identity->role === User::ROLE_SUPERVISOR_GLOBAL)) {
                                return true;
                            } else {
                                return false;
                            }
                        },
                    ],
                    [
                        'actions' => ['logout', 'switch-user'],
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
        return $this->redirect(Yii::$app->params['supervisorDomain'] . 'ms-survey');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->redirect(Yii::$app->params['loginDomain']);
//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            $model->password = '';
//
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
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

    public function actionChangeLanguage($ln)
    {
        Yii::$app->session->set('lang', $ln);
        Yii::$app->language = $ln;
        return Yii::$app->session->get('lang');
    }

    public function actionSwitchUser()
    {
        $originalId = Yii::$app->session->get('user.idbeforeswitch');
        $survey_id = Yii::$app->session->get('user.surveybeforeswitch');
        if ($originalId) {
            $userModel = User::findOne($originalId);
            $duration = 0;
            Yii::$app->user->switchIdentity($userModel, $duration);
            Yii::$app->session->remove('user.idbeforeswitch');
            Yii::$app->session->remove('user.surveybeforeswitch');
        }
        $this->redirect(Yii::$app->params['adminDomain'] . 'survey/management?id=' . $survey_id);
    }

}
