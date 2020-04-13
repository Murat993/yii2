<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        ///////////////////////////////////////////////////////////
        //Global Permissions
        $adPerm = $auth->createPermission('admin-permissions');
        $auth->add($adPerm);
        $supervisorPerm = $auth->createPermission('supervisor-permissions');
        $auth->add($supervisorPerm);
        $mysticShopperPerm = $auth->createPermission('ms-permissions');
        $auth->add($mysticShopperPerm);
        $clientUserPerm = $auth->createPermission('client-user-permissions');
        $auth->add($clientUserPerm);
        $clientSuperPerm = $auth->createPermission('client-superuser-permissions');
        $auth->add($clientSuperPerm);
        ///////////////////////////////////////////////////////////
        //Main Hierarchy
        $supervisor = $auth->createRole('supervisor');
        $auth->add($supervisor);
        $auth->addChild($supervisor, $supervisorPerm);
        $mysticShopper = $auth->createRole('mystic-shopper');
        $auth->add($mysticShopper);
        $auth->addChild($mysticShopper, $mysticShopperPerm);
        ///////////////////////////////////////////////////////////
        //Client Hierarchy
        $clientUser = $auth->createRole('client-user');
        $auth->add($clientUser);
        $auth->addChild($clientUser, $clientUserPerm);
        $clientSuper = $auth->createRole('client-super');
        $auth->add($clientSuper);
        $auth->addChild($clientSuper, $clientSuperPerm);
            $auth->addChild($clientSuper, $clientUser);
        ///////////////////////////////////////////////////////////
        //Admin all permissions
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $adPerm);
        $auth->addChild($admin, $supervisor);
        $auth->addChild($admin, $mysticShopper);
        $auth->addChild($admin, $clientUser);
        $auth->addChild($admin, $clientSuper);
        ///////////////////////////////////////////////////////////

    }

    public function actionShow()
    {
        $auth = Yii::$app->authManager;
        print_r($auth->getPermissionsByUser(12));
    }

}
