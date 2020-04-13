<?php

namespace common\services\notifications;

use common\services\notifications\services\email\MailSender;
use common\services\notifications\services\sms\SmsSender;
use common\services\notifications\services\voice\VoiceSender;
use common\services\notifications\services\push\PushSender;
use common\services\notifications\services\telegram\TelegramSender;
use common\services\notifications\services\freeswitch\FreeSwitchSender;

use Yii;

class NotificationService
{
    use \common\services\notifications\traits\NService;

    protected $_errors = [];
    protected $_info = [];

    const EMAIL = 1;
    const SMS = 2;
    const VOICE = 4;
    const PUSH = 3;
    const EMAIL_WITH_ATTACHMENT = 7;

    private $_service;

    public function sendMessage($sender = self::EMAIL, $params)
    {
        switch ($sender) {
            case self::EMAIL:
                $this->_service = new MailSender();
                break;
            case self::SMS:
                $this->_service = new SmsSender();
                break;
            case self::PUSH:
                $this->_service = new PushSender();
                break;
            default :
                $this->_service = new MailSender();
                break;
        }

        try {
            $this->_info = $this->_service->send($params);
            return true;
        } catch (Exception $ex) {
            $this->_errors[] = $ex->getMessage();
            return false;
        }
    }

    public function sendMailWithAttachment($params)
    {
        $this->_service = new MailSender();

        try {
            $this->_info = $this->_service->sendWithAttachment($params);
            return true;
        } catch (Exception $ex) {
            $this->_errors[] = $ex->getMessage();
            return false;
        }
    }


    public function getErrors()
    {
        return $this->_errors;
    }

    public function getInfo()
    {
        return $this->_info;
    }
}