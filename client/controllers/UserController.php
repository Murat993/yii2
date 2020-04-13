<?php

namespace client\controllers;

use client\models\ResetPasswordForm;
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
                        'actions' => ['create', 'update', 'delete', 'permissions'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->can('client-superuser-permissions') && Yii::$app->userService->checkClientEdit()) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ],
                    [
                        'actions' => ['index', 'view', 'reset-password'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $userRole = Yii::$app->user->identity->role;
                            if (Yii::$app->user->can('client-user-permissions') && ($userRole === User::ROLE_CLIENT_USER || $userRole === User::ROLE_CLIENT_SUPER)) {
                                return true;
                            } else {
                                return false;
                            }
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $post = Yii::$app->request->post('statusFilter');
        $this->view->title = Yii::t('app', 'Список пользователей');
        $this->view->params['breadcrumbs'] = [
            $this->view->title
        ];

        $query = User::find();
        $query->innerJoin('client_user', "user.id = client_user.user_id");
        $query->innerJoin('client', "client_user.client_id = client.id");
        $query->where([
            'client.id' => $this->getClientId()
        ]);
        $query->andWhere(['in', 'user.role', [
            User::ROLE_CLIENT_USER,
            User::ROLE_MYSTIC,
            User::ROLE_SUPERVISOR
        ]])->orderBy('id DESC');
        if ($post) {
            $query->andFilterWhere(['user.status' => $post]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);
        $statuses = Yii::$app->utilityService->getStatusList();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'statuses' => $statuses
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->view->title = Yii::t('app', 'Просмотр пользователя');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Список пользователей'),
                'url' => ['index']
            ],
            $this->view->title
        ];
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $this->view->title = Yii::t('app', 'Добавить пользователя');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Список пользователей'),
                'url' => ['index']
            ],
            $this->view->title
        ];
        $model = new User();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && Yii::$app->userService->createClientUser($model, $this->getClientId())) {
            return $this->redirect(['index']);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->view->title = Yii::t('app', 'Редактирование пользователя');
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Список пользователей'),
                'url' => ['index']
            ],
            $this->view->title
        ];
        $model = $this->findModel($id);
        $oldRole = $model->role;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->role !== $oldRole) {
                Yii::$app->userService->setRole($model->id, $model->role);
            }
            return $this->redirect(['index']);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPermissions($id)
    {

        $client_id = $this->getClientId();
        $model = $this->findModel($id);
        $this->view->title = Yii::t('app', 'Права пользователя') . " " . $model->name . ' / ' . $model->email;
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Список пользователей'),
                'url' => ['index']
            ],
            $this->view->title
        ];
        $clientUser = ClientUser::find()->where(['client_id' => $client_id, 'user_id' => $id])->one();
        if (Yii::$app->request->post()) {
            $arr = explode(",", Yii::$app->request->post("filial_ids"));
            Yii::$app->userService->createClientUserFilials($arr, $clientUser->id);
            return $this->redirect(['index']);
        }
        $filialIds = [];
        $client_user_filials = ClientUserFilial::find()->where([
            'client_user_id' => $clientUser->id
        ])->all();
        if ($client_user_filials) {
            foreach ($client_user_filials as $ef) {
                $filialIds[] = $ef->filial_id;
            }
        }
        return $this->render('permissions', [
            'model' => $model,
            'tree' => Yii::$app->structService->getWidgetTreeDataWithFilials($client_id, $filialIds)
        ]);
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

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ($id == Yii::$app->user->identity->id) {
            throw new ForbiddenHttpException("Невозможно удалить свою запись");
        }
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
