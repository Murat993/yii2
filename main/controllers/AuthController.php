<?php

namespace main\controllers;

use common\models\Client;
use common\models\User;
use common\services\notifications\NotificationService;
use main\models\RecoveryForm;
use main\models\ResetForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use main\models\LoginForm;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class AuthController extends BaseController
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
                        'actions' => ['login', 'error', 'password-reset', 'validate-form', 'renew-client-access'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['recovery'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            try {
                                $this->user = $this->checkToken();
                            } catch (Exception $ex) {
                                return $this->response($ex->getCode(), null, $ex->getMessage());
                            }
                            return true;
                        }
                    ],
                    [
                        'actions' => ['logout', 'index', 'renew-client-access'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
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
        return $this->redirector(Yii::$app->user->getIdentity()->role);

    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirector(Yii::$app->user->getIdentity()->role);
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    private function redirector($role)
    {
        switch ($role) {
            case User::ROLE_ADMIN:
                return $this->redirect(Yii::$app->params['adminDomain']);
                break;
            case User::ROLE_SUPERVISOR:
                return $this->redirect(Yii::$app->params['supervisorDomain']);
                break;
            case User::ROLE_SUPERVISOR_GLOBAL:
                return $this->redirect(Yii::$app->params['supervisorDomain']);
                break;
            case User::ROLE_CLIENT_USER:
                return $this->redirect(Yii::$app->params['clientDomain']);
                break;
            case User::ROLE_CLIENT_SUPER:
                if (Yii::$app->user->can('admin-permissions')) {
                    return $this->redirect(Yii::$app->params['adminDomain']);
                }
                return $this->redirect(Yii::$app->params['clientDomain']);
                break;
            case User::ROLE_MYSTIC:
                if (!Yii::$app->user->identity->agreement_accepted) {
                    Yii::$app->session->set('not-accepted', true);
                }
                return $this->redirect(Yii::$app->params['msDomain']);
                break;
            case User::ROLE_MYSTIC_GLOBAL:
                if (!Yii::$app->user->identity->agreement_accepted) {
                    Yii::$app->session->set('not-accepted', true);
                }
                return $this->redirect(Yii::$app->params['msDomain']);
                break;
        }
    }

    public function actionPasswordReset()
    {
        $model = new ResetForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = Yii::$app->authService->passwordRecovery($model->email);
            if ($sent) {
                return $this->render('after-password-reset', [
                    'message' => Yii::t('app', 'passwordRecoverySuccess')
                ]);
            } else {
                return $this->render('after-password-reset', [
                    'message' => Yii::t('app', 'passwordRecoveryFail')
                ]);
            }
        }
        return $this->render('password-reset', [
            'model' => $model
        ]);
    }

    public function actionValidateForm()
    {
        $model = new ResetForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionRecovery($token)
    {
        $model = new RecoveryForm();
        if ($model->load(Yii::$app->request->post())) {
            $bool = Yii::$app->userService->setNewPassword($model->password, $this->user);
            if ($bool) {
                Yii::$app->session->setFlash('success', '');
                return $this->redirect('index');
            } else {
                Yii::$app->session->setFlash('error', '');
                return $this->redirect('index');
            }
        }

        return $this->render('pass-recovery', [
            'model' => $model
        ]);
    }

    public function checkToken()
    {
        $token = Yii::$app->request->get('token');
        if ($token === null || $token === '') {
            throw new HttpException(403, Yii::t('app', 'Неверный токен'), 403);
        }
        $user = Yii::$app->userService->getUserByResetToken($token);
        if ($user) {
            return $user;
        } else {
            throw new HttpException(403, Yii::t('app', 'Неверный токен'), 403);
        }
    }

    public function actionRenewClientAccess($token)
    {
        $client = Client::findOne(['resubscribe_token' => $token]);
        if ($client) {
            $from = Yii::$app->params['supportEmail'];
            $subject = 'Заявка на продление тарифа';
            $message = "Вам поступила заявка на продление тарифа от клиента. ID-{$client->id}: {$client->name} ({$client->phone}, {$client->email})";
            $messageSent = Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
                'from' => $from,
                'to' => 'ms@plan.com.kz',
                'subject' => $subject,
                'message' => $message
            ]);
            if ($messageSent) {
                $client->resubscribe_token = null;
                if ($client->update()) {
                    return $this->render('after-resub', [
                        'message' => Yii::t('app', 'resubSuccess')
                    ]);
                }
            }

        } else {
            return $this->render('after-resub', [
                'message' => Yii::t('app', 'resubFailure')
            ]);
        }
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

}
