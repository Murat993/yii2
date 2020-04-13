<?php
namespace ms\controllers;

use common\models\User;
use ms\models\RequestPasswordResetForm;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
                        'matchCallback' => function($rule, $action) {
                            if (Yii::$app->user->can('ms-permissions') && (Yii::$app->user->identity->role === User::ROLE_MYSTIC || Yii::$app->user->identity->role === User::ROLE_MYSTIC_GLOBAL)){
                                return true;
                            }else{
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
        $this->redirect(Yii::$app->params['msDomain'] . 'survey');
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

    public function actionResetPasswordRequest(){
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new RequestPasswordResetForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            try{
                Yii::$app->userService->resetPassword($model->user);
                $this->redirect('login');
            }catch (Exception $e){
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

}
