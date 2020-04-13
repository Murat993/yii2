<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 20.06.18
 * Time: 11:29
 */

namespace common\services;


use common\models\Client;
use common\models\ClientGroupTemplate;
use common\models\ClientColor;
use common\models\ClientUser;
use common\models\ClientUserFilial;
use common\models\EmployeeFilial;
use common\models\GlobalColor;
use common\models\User;
use common\models\UserColor;
use common\services\notifications\NotificationService;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class UserService
{
    public function getUser($id)
    {
        return User::findOne(['id' => $id]);
    }

    public function findByEmail($email)
    {
        return User::findOne(['email' => $email]);
    }

    public function createUser($model, $sendEmail = null)
    {
        $zone = new \DateTimeZone('Asia/Almaty');
        $currentDate = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
        $pass = $model->generatePass();
        $model->setPassword($pass);
        if ($model->status == '1') {
            $model->status = User::STATUS_ACTIVE;
        } elseif ($model->status == '0') {
            $model->status = User::STATUS_DEACTIVATED;
        }
        $model->registration_date = $currentDate;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save()) {
                if ((int)$model->role === User::ROLE_MYSTIC || (int)$model->role === User::ROLE_MYSTIC_GLOBAL) {
                    $model->linkCity();
                }
                if (Yii::$app->authService->givePermissions($model->id, $this->getAuthRole($model->role))) {
                    $transaction->commit();
                    if ($sendEmail == '1') {
                        $this->sendPassword($model->email, $pass, $model->role);
                        return $this->sendPasswordToAdmin($model, $pass);
                    } else {
                        return $this->sendPassword($model->email, $pass, $model->role);
                    }
                }

            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    public function sendPassword($email, $pass, $role)
    {
        $lang = \Yii::$app->language;
        $from = Yii::$app->params['supportEmail'];
        return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
            'from' => $from,
            'to' => $email,
            'subject' => Yii::t('app', 'PLAN - регистрация нового пользователя.'),
            'compose_view' => [
                'html' => '@common/mail/views/html/send-password-' . $lang,
                'text' => '@common/mail/views/text/send-password-' . $lang
            ],
            'compose_params' => [
                'password' => $pass,
                'role' => Yii::$app->utilityService->getRoleLabel($role)
            ]
        ]);
    }

    public function sendPasswordToAdmin($model, $pass)
    {
        $from = Yii::$app->params['supportEmail'];
        $subject = 'PLAN: Пароль';
        $adminEmail = Yii::$app->systemSettingsService->getSystemSettings()->adminEmail;
        $message = "Пользователь - {$model->name}, email: {$model->email}. Пароль для входа: {$pass}";
        return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
            'from' => $from,
            'to' => $adminEmail,
            'subject' => $subject,
            'message' => $message
        ]);
    }


    public function setRole($id, $role)
    {
        return Yii::$app->authService->givePermissions($id, $this->getAuthRole($role));
    }

    public function updateRole($id, $role)
    {
        return Yii::$app->authService->updatePermissions($id, $this->getAuthRole($role));
    }

    public function getAuthRole($role)
    {
        switch ($role) {
            case User::ROLE_ADMIN:
                return AuthService::ROLE_ADMIN;
                break;
            case User::ROLE_SUPERVISOR:
                return AuthService::ROLE_SUPERVISOR;
                break;
            case User::ROLE_SUPERVISOR_GLOBAL:
                return AuthService::ROLE_SUPERVISOR;
                break;
            case User::ROLE_MYSTIC:
                return AuthService::ROLE_MYSTIC;
                break;
            case User::ROLE_MYSTIC_GLOBAL:
                return AuthService::ROLE_MYSTIC;
                break;
            case User::ROLE_CLIENT_USER:
                return AuthService::ROLE_CLIENT_USER;
                break;
            case User::ROLE_CLIENT_SUPER:
                return AuthService::ROLE_CLIENT_SUPER;
                break;
        }
    }

    public function resetPassword($user)
    {
        $pass = $user->generatePass();
        $user->setPassword($pass);
        if ($user->save()) {
            $from = Yii::$app->params['supportEmail'];
            $subject = 'PLAN';
            $message = "Пароль для входа: {$pass}";
            return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
                'from' => $from,
                'to' => $user->email,
                'subject' => $subject,
                'message' => $message
            ]);
        }
    }

    public function setNewPassword($pass, $user)
    {
        $user->setPassword($pass);
        $user->reset_token = null;
        if ($user->save()) {
            $from = Yii::$app->params['supportEmail'];
            $subject = 'PLAN: Сброс пароля';
            $message = "Новый пароль для входа: {$pass}";
            return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
                'from' => $from,
                'to' => $user->email,
                'subject' => $subject,
                'message' => $message
            ]);
        }
    }

    public function getColorByPercent($percent)
    {
        if ($userColor = UserColor::find()
            ->where(['<=', 'procent', (int)$percent])
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->orderBy(['procent' => SORT_DESC])
            ->one()
        ) return $userColor;
        elseif (is_null($userColor)) {
            $userColorAsc = UserColor::find()
                ->where(['>=', 'procent', (int)$percent])
                ->andWhere(['user_id' => Yii::$app->user->getId()])
                ->orderBy(['procent' => SORT_ASC])
                ->one();
            if (is_null($userColorAsc)) {
                $clientUser = ClientUser::findOne(['user_id' => Yii::$app->user->getId()]);
                if ($clientColor = ClientColor::find()
                    ->where(['<=', 'procent', (int)$percent])
                    ->andWhere(['client_id' => $clientUser->client_id])
                    ->orderBy(['procent' => SORT_DESC])
                    ->one()
                ) return $clientColor;
                elseif (is_null($clientColor)) {
                    $clientColorAsc = ClientColor::find()
                        ->where(['>=', 'procent', (int)$percent])
                        ->andWhere(['client_id' => $clientUser->client_id])
                        ->orderBy(['procent' => SORT_ASC])
                        ->one();
                    if (is_null($clientColorAsc)) {
                        $globalColor = GlobalColor::find()
                            ->where(['<=', 'procent', (int)$percent])
                            ->orderBy(['procent' => SORT_DESC])
                            ->one();
                        if (is_null($globalColor)) {
                            return GlobalColor::find()
                                ->where(['>=', 'procent', (int)$percent])
                                ->orderBy(['procent' => SORT_ASC])
                                ->one();
                        }
                        return $globalColor;
                    }
                    return $clientColorAsc;
                }
                return $clientColor;
            }
            return $userColorAsc;
        }
    }

    public function getClientColorMap()
    {
        $clientColors = ClientColor::find()->where(['client_id' => Yii::$app->user->identity->getClientId()])->orderBy('procent DESC')->all();
        if (!$clientColors) {
            return GlobalColor::find()->all();
        } else {
            return $clientColors;
        }
    }

    public function getSupervisors()
    {
        return User::find()->where(['role' => User::ROLE_SUPERVISOR])->all();
    }

    public function getSupervisorsByClient($client_id)
    {
        return ClientUser::find()->where(['role' => User::ROLE_SUPERVISOR, 'client_id' => (int)$client_id])->all();
    }

    public function getSupervisorsGlobal()
    {
        return User::find()->where(['role' => User::ROLE_SUPERVISOR_GLOBAL])->all();
    }

    public function getSupervisorsGlobalAsMap()
    {
        $sups = $this->getSupervisorsGlobal();
        if ($sups) {
            return ArrayHelper::map($sups, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getSupervisorsAsMap($client_id)
    {
        $supervisors = $this->getSupervisorsByClient($client_id);
        $users = [];
        foreach ($supervisors as $item) {
            $users[] = $item->user;
        }
        if ($users) {
            return ArrayHelper::map($users, 'id', 'name');
        } else {
            return [];
        }
    }

    public function updatePassword($user, $newPassword)
    {
        $user->setPassword($newPassword);
        if ($user->save()) {
            $from = Yii::$app->params['supportEmail'];
            $subject = 'PLAN';
            $message = "Пароль для входа: {$newPassword}";
            return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
                'from' => $from,
                'to' => $user->email,
                'subject' => $subject,
                'message' => $message
            ]);
        }
    }

    /**
     * @param User $model
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function createClientUser($model, $client_id, $sendEmail = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = $this->createUser($model, $sendEmail);
            $clientUser = new ClientUser();
            $clientUser->client_id = (int)$client_id;
            $clientUser->user_id = (int)$model->id;
            $clientUser->role = (int)$model->role;
            $clientUser->save();
            $transaction->commit();
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function createClientAdmin($adminId, $clientId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $clientUser = new ClientUser();
            $clientUser->client_id = (int)$clientId;
            $clientUser->user_id = (int)$adminId;
            $clientUser->role = User::ROLE_CLIENT_SUPER;
            $clientUser->save();

            $admin = User::findOne(['id' => $adminId]);
            $admin->role = User::ROLE_CLIENT_SUPER;
            $admin->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function deleteClientAdmin($adminId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            ClientUser::deleteAll(['user_id' => $adminId]);

            $admin = User::findOne(['id' => $adminId]);
            $admin->role = User::ROLE_ADMIN;
            $admin->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function createClientUserFilials($filialIds, $clientUserId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            ClientUserFilial::deleteAll(['client_user_id' => $clientUserId]);
            foreach ($filialIds as $filialId) {
                $model = new ClientUserFilial();
                $model->filial_id = $filialId;
                $model->client_user_id = $clientUserId;
                $model->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function msMapByClient($id, $city_id = null)
    {
        $query = ClientUser::find()->where(['client_id' => $id])
            ->andWhere(['client_user.role' => User::ROLE_MYSTIC]);
        if ($city_id) {
            $query->innerJoin('user', 'user.id = client_user.id')
                ->innerJoin('geo_user', 'geo_user.user_id = user.id')
                ->andWhere(['geo_user.geo_unit_id' => $city_id]);
        }
        $list = $query->all();
        if ($list) {
            $users = [];
            foreach ($list as $item) {
                $users[] = $item->user;
            }
            return ArrayHelper::map($users, 'id', 'name');
        } else {
            return [];
        }
    }

    public function msMapGlobal($id_city = null)
    {
        $query = User::find()->where(['role' => User::ROLE_MYSTIC_GLOBAL]);
        if ($id_city) {
            $query->innerJoin('geo_user', 'geo_user.user_id = user.id')
                ->andWhere(['geo_user.geo_unit_id' => $id_city]);
        }
        $list = $query->all();
        if ($list) {
            return ArrayHelper::map($list, 'id', 'name');
        } else {
            return [];
        }
    }

    public function employeesMapByFilial($id)
    {
        $list = EmployeeFilial::findAll(['filial_id' => $id]);
        if ($list) {
            $users = [];
            foreach ($list as $item) {
                $users[] = $item->employee;
            }
            return ArrayHelper::map($users, 'id', 'name');
        } else {
            return [];
        }
    }

    public function manageClientCreation($client)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $groupIds = $client->groups;
            $client->save();
            ClientGroupTemplate::deleteAll(['client_id' => $client->id]);
            foreach ($groupIds as $group) {
                $ClientGroups = new ClientGroupTemplate();
                $ClientGroups->client_id = $client->id;
                $ClientGroups->group_template_id = $group;
                $ClientGroups->save();
            }

            $user = new User();
            $user->phone = $client->phone;
            $user->email = $client->email;
            $user->name = $client->name;
            $user->status = $client->status;
            if ($client->superuser == '1') {
                $user->role = User::ROLE_CLIENT_SUPER;
            } elseif ($client->superuser == '0') {
                $user->role = User::ROLE_CLIENT_USER;
            }
            $transaction->commit();
            return $this->createClientUser($user, $client->id, $client->pass_email);
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getUserByResetToken($token)
    {
        return User::findOne(['reset_token' => $token]);
    }

    /**
     * Провека статуса компании для авторизации
     * @param $form - форма авторизации
     * @return bool
     */
    public function checkClient($form)
    {
        $user = User::findOne(['email' => $form->email]);
        if ($user->role == User::ROLE_ADMIN || empty($user->client) || $user->client->status == Client::STATUS_ACTIVATED) {
            return true;
        }
        return false;
    }

    /**
     * Провека статуса редактирования компании для ROLE_CLIENT_SUPER
     * @param $form - форма авторизации
     * @return bool
     */
    public function checkClientEdit()
    {
        if (Yii::$app->user->identity->client->can_edit == Client::STATUS_EDIT_ACTIVATED && Yii::$app->user->identity->role === User::ROLE_CLIENT_SUPER) {
            return true;
        }
        return false;
    }

    public function acceptAgreement($user_id)
    {
        $user = $this->getUser((int)$user_id);
        if ($user) {
            $user->agreement_accepted = true;
            return $user->save();
        } else {
            return false;
        }
    }
}