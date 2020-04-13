<?php

namespace ms\controllers;

use ms\models\ResetPasswordForm;
use common\models\ClientUser;
use common\models\ClientUserFilial;
use common\services\AuthService;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
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
                        'actions' => ['reset-password', 'accept-agreement', 'refuse-agreement'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'accept-agreement' => ['POST'],
                    'refuse-agreement' => ['POST']
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id === 'accept-agreement' || $action->id === 'refuse-agreement') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }


    public function actionResetPassword()
    {
        $user = Yii::$app->user->identity;
        $model = new ResetPasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->userService->updatePassword($user, $model->password);
            Yii::$app->user->logout();
            return $this->goHome();
        }
        return $this->render('resetPassword', ['model' => $model]);
    }

    public function actionAcceptAgreement()
    {
        $result = Yii::$app->userService->acceptAgreement(Yii::$app->user->getId());
        if ($result) {
            Yii::$app->session->remove('not-accepted');
        }
        return $result;
    }

    public function actionRefuseAgreement()
    {
        return Yii::$app->user->logout();
    }


}
