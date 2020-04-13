<?php
namespace common\services\notifications\services\telegram;

use common\services\notifications\services\telegram\gates\Telegram;
use \yii\base\Exception;

/**
 * Yii class to work with Telegram API
 */
class TelegramSender 
{
    use \common\services\notifications\traits\NService;
    /**
     * Chat id Alexey 17328698
     * Chat id Pasha 124381799
     * Chat id Anton 160975205
     */
    const ALEXEY_CHATID = 17328698;
    const ANTON_CHATID = 160975205;
    const PASHA_CHATID = 124381799;
    
    /**
     * Global object
     * @var Telegram 
     */
    public $telegram ;
    /**
     * Bot name
     * @var string 
     */
    public $name = 'myplaces_bot';
    /**
     * Bot token
     * @var string 
     */
    public $token = '136131506:AAHLC_iPU9R8Ev9ueyTuS32yCnhnnv5Ul6Y';

    /**
     * Отправка сообщения
     * @param string $message ( max 4092 )
     * @param integer $chat_id
     */
    public function send( $params){

        $chatIds=$this->getParam($params, 'chatIds');
        $message=$this->getParam($params, 'message');
        
        
        
        if (!$chatIds || !$message)   throw new Exception("Недостаточно данных для работы telegram!");
        
        $this->telegram = new Telegram( $this->token ); 
        
        $recipients=[];
        if (is_array($chatIds))
        {
            $recipients=$chatIds;
        }else
        {
            $recipients[]=$chatIds;
        }
        
        foreach ($recipients as $chatId) {
            $this->telegram->sendMessage($message , $chatId);
        }
        return true;

    }
    
    public function getUpdates( ){
        return $this->telegram->getUpdates();
    }
}

