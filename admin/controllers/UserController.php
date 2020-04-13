<?php

namespace admin\controllers;

use admin\models\ChangePasswordForm;
use common\models\GeoUser;
use common\models\GlobalColor;
use common\models\SignUpForm;
use common\services\AuthService;
use Yii;
use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
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
                        'actions' => ['create-admin'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'index', 'view', 'create', 'update', 'delete', 'validate', 'add-city',
                            'unlink-geo', 'reset-password', 'change-password', 'validate-pass-change',
                            'color','delete-color'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            return  Yii::$app->user->can('admin-permissions');
                        }
                    ],
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

        $query = User::find()->orderBy('registration_date DESC');
        if ($post) {
            $query->andFilterWhere(['status' => $post]);
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

    public function actionColor()
    {
        if  (Yii::$app->request->isAjax) {
            $color = Yii::$app->request->get('color');
            $procent = Yii::$app->request->get('procent');
            for($i = 0; $i < count($color);$i++) {
                $colorUser = new GlobalColor();
                $colorUser->color = $color[$i];
                $colorUser->procent = $procent[$i];
                $colorUser->save();
            }
        }

        $globalColor = GlobalColor::find()->all();

        if (Model::loadMultiple($globalColor, Yii::$app->request->post()) && Model::validateMultiple($globalColor)) {
            foreach ($globalColor as $global) {
                $global->update();
            }
            return $this->redirect(['color']);
        }

        return $this->render('change-color', [
            'globalColor' => $globalColor,
        ]);
    }

    public function actionDeleteColor()
    {
        if (Yii::$app->request->isAjax) {
            $colorId = Yii::$app->request->get('colorId');
            $colorModel = GlobalColor::findOne((int)$colorId);
            $colorModel->delete();
            return true;
        }
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

        if ($model->load(Yii::$app->request->post()) && Yii::$app->userService->createUser($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreateAdmin()
    {
        $model = new User();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $zone = new \DateTimeZone('Asia/Almaty');
            $currentDate = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
            $pass = $model->generatePass();
            $model->setPassword($pass);
            $model->status = User::STATUS_ACTIVE;
            $model->registration_date = $currentDate;
            $model->role = User::ROLE_ADMIN;
            if ($model->save() && Yii::$app->authService->givePermissions($model->id, AuthService::ROLE_ADMIN)) {
                Yii::$app->userService->sendPassword($model->email, $pass, $model->role);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create_admin', [
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
        $query = GeoUser::find()->where(['user_id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);
        $oldRole = $model->role;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ((int)$model->role !== $oldRole){
                Yii::$app->userService->updateRole($model->id, $model->role);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAddCity($user_id)
    {
        $model = new GeoUser();
        $model->user_id = $user_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '');
            $this->redirect(['update', 'id' => $user_id]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_add_city', [
                'model' => $model
            ]);
        }
    }

    public function actionChangePassword($user_id)
    {
        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = Yii::$app->userService->getUser($user_id);
            if ($user && Yii::$app->userService->setNewPassword($model->password, $user)){
                Yii::$app->session->setFlash('success', '');
                $this->redirect(['update', 'id' => $user_id]);
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_change-pass', [
                'model' => $model
            ]);
        }
    }

    public function actionValidatePassChange()
    {
        $model = new ChangePasswordForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionUnlinkGeo($id, $user_id)
    {
        $geo = GeoUser::deleteAll(['id' => $id]);
        if ($geo){
            $this->redirect(['update', 'id' => $user_id]);
        }
    }

    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->userService->resetPassword($model)){
            Yii::$app->session->setFlash('success', 'Пароль сброшен');
            return $this->redirect(['update', 'id'=>$model->id]);
        }
    }

    public function actionValidate()
    {
        $model = new User();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
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
