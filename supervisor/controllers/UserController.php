<?php

namespace supervisor\controllers;

use Yii;
use yii\db\Query;
use common\models\User;
use yii\filters\AccessControl;
use supervisor\models\ResetPasswordForm;

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
                        'actions' => ['reset-password', 'search-dropdown'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            if (Yii::$app->user->can('supervisor-permissions') && (Yii::$app->user->identity->role === User::ROLE_SUPERVISOR || Yii::$app->user->identity->role === User::ROLE_SUPERVISOR_GLOBAL)){
                                return true;
                            }else{
                                return false;
                            }
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Сброс пароля
     * @return string|\yii\web\Response
     */
    public function actionResetPassword()
    {
        $user = Yii::$app->user->identity;
        $model = new ResetPasswordForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            Yii::$app->userService->updatePassword($user, $model->password);
            Yii::$app->user->logout();
            return $this->goHome();
        }
        return $this->render('resetPassword', ['model' => $model]);
    }

    /**
     * Возвращает массив пользователей по заданным параметрам
     * @param null $q              - user.name из таблицы User
     * @param null $id
     * @param null $role           - роль можно через перечислить через запятую
     * @param null $client_id
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSearchDropdown($q = null, $id = null, $role = null, $client_id = null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('user.id AS id, user.name AS text')
                ->from('user')
                ->leftJoin('client_user', 'user.id = client_user.user_id')
                ->where(['like', 'user.name', $q])
                ->limit(20);
            if($role){
                $role = explode(',', $role);
                $query->andWhere(['user.role' => $role]);
            }
            if($client_id){
                $query->andWhere(['client_user.client_id' => $client_id]);
            }
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->name];
        }
        return $out;
    }
}
