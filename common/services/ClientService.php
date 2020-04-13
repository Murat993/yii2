<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 27.06.18
 * Time: 9:34
 */

namespace common\services;


use common\models\Client;
use common\models\ClientGroupTemplate;
use common\models\Position;
use common\models\User;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

use common\models\ClientUser;

class ClientService
{
    public function getClients()
    {
        return Client::find()->all();
    }

    public function getClientsAsMap()
    {
        $clients = $this->getClients();
        if ($clients) {
            return ArrayHelper::map($clients, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getClient($id)
    {
        return Client::findOne(['id' => $id]);
    }

    public function createClientUser($client_id, $user_id)
    {
        $clientUser = new ClientUser();
        $clientUser->client_id = $client_id;
        $clientUser->user_id = $user_id;
        $clientUser->save();
    }

    public function getClientsByGroup($id)
    {
        $clientGroups = ClientGroupTemplate::findAll(['group_template_id' => $id]);
        $cgClients = [];
        if ($clientGroups) {
            foreach ($clientGroups as $item) {
                $cgClients[] = $item->client;
            }
        }
        $oldClients = Client::findAll(['group_id' => $id]);
        return ArrayHelper::merge($cgClients, $oldClients);
    }

    public function getClientsByGroupAsMap($group_id)
    {
        $clients = $this->getClientsByGroup($group_id);
        if ($clients) {
            return ArrayHelper::map($clients, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getClientUsers($id)
    {
        return ClientUser::find()->where(['client_id' => $id])->all();
    }

    public function updateClient(Client $client)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $groupIds = $client->groups;
            $client->group_id = null;
            ClientGroupTemplate::deleteAll(['client_id' => $client->id]);
            foreach ($groupIds as $group) {
                $ClientGroups = new ClientGroupTemplate();
                $ClientGroups->client_id = $client->id;
                $ClientGroups->group_template_id = $group;
                if (!$ClientGroups->save()) {
                    throw new Exception(Yii::t('app', 'Ошибка сохранения'));
                }
            }

            if (!$client->update()) {
                throw new Exception(Yii::t('app', 'Ошибка сохранения'));
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getColorByPercent($percent, $palette)
    {
        foreach ($palette as $i => $color) {
            if ((int)$percent >= $color->procent) {
                return $color->color;
            }elseif ((count($palette)-1) === $i){
                return $color->color;
            }
        }
    }
}