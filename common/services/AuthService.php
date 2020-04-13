<?php

namespace common\services;

use common\models\User;
use common\services\notifications\NotificationService;
use Yii;

class AuthService
{

    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_MYSTIC = 'mystic-shopper';
    const ROLE_CLIENT_USER = 'client-user';
    const ROLE_CLIENT_SUPER = 'client-super';

    public function givePermissions($id_user, $type)
    {
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole($type);
        return $authManager->assign($role, $id_user);
    }

    public function updatePermissions($id_user, $type)
    {
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole($type);
        if ($authManager->revokeAll($id_user)) {
            return $authManager->assign($role, $id_user);
        }else{
            return false;
        }

    }

    public function passwordRecovery($email)
    {
       $user = Yii::$app->userService->findByEmail($email);
       $token = $user->generateRecoveryToken();
       $user->reset_token = $token;
       if ($user->save()){
           $from = Yii::$app->params['supportEmail'];
           $subject = 'PLAN: Восстановление пароля';
           $message = "Для восстановления пароля перейдите по ссылке: " . '' . Yii::$app->params['loginDomain'] . "auth/recovery?token={$token}";
           return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
               'from' => $from,
               'to' => $email,
               'subject' => $subject,
               'message' => $message
           ]);
       }
    }

}
