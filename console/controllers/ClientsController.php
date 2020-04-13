<?php

namespace console\controllers;

use Codeception\Module\Cli;
use common\models\Client;
use Yii;
use yii\console\Controller;
use common\services\notifications\NotificationService;

class ClientsController extends Controller
{

    public function actionManageClientSubs()
    {
        $zone = new \DateTimeZone('Asia/Almaty');
        $currentTime = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
        $this->notifyClients($currentTime);
        $this->disableSubs($currentTime);
    }

    private function notifyClients($currentTime)
    {
        $notifyTime = \DateTime::createFromFormat('Y-m-d H:i:s', $currentTime)->modify('+3 days')->format('Y-m-d H:i:s');
        $clients = Client::find()
            ->where(['<=', 'tariff_exp', $notifyTime])
            ->andWhere(['status' => Client::STATUS_ACTIVATED])
            ->andWhere(['!=', 'notified', 1])
            ->all();
        if ($clients) {
            foreach ($clients as $client) {
                $date = new \DateTime($client->tariff_exp);
                $expDate = $date->format('Y-m-d');
                $client->generateResubToken();
                $messageSent = Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
                    'from' => Yii::$app->params['supportEmail'],
                    'to' => $client->email,
                    'subject' => Yii::t('app', 'Тариф подходит к концу'),
                    'compose_view' => [
                        'html' => '@common/mail/views/html/tariff-expires-ru-RU',
                        'text' => '@common/mail/views/text/tariff-expires-ru-RU'
                    ],
                    'compose_params' => [
                        'id' => $client->id,
                        'exp_date' => $expDate,
                        'resubToken' => $client->resubscribe_token
                    ]
                ]);
                if ($messageSent) {
                    $client->notified = 1;
                    $client->update();
                }
            }
        }
    }

    private function disableSubs($currentTime)
    {
        Client::updateAll(['status' => Client::STATUS_LOCKED], ['<=', 'tariff_exp', $currentTime]);
    }

    public function actionTestMail()
    {
        return Yii::$app->notification->sendMessage(NotificationService::EMAIL, [
            'from' => Yii::$app->params['supportEmail'],
            'to' => 'blacqocelot@gmail.com',
            'subject' => Yii::t('app', 'Новое задание № ' . 666),
            'compose_view' => [
                'html' => '@common/mail/views/html/send-ms-new-task-' . 'ru-RU',
                'text' => '@common/mail/views/text/send-ms-new-task-' . 'ru-RU'
            ],
            'compose_params' => [
                'id' => 666,
                'ms_survey_id' => 666
            ]
        ]);
    }

}
