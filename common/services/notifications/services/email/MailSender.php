<?php
namespace common\services\notifications\services\email;

use common\services\notifications\services\email\gates\SwiftMailer;
use \yii\base\Exception;

class MailSender  {
    use \common\services\notifications\traits\NService;
    
    const GATE_SWIFT_MAILER='SwiftMailer';
    
    private $_mailer;
    
    
    function send($params)
    {
        $gate=$this->getParam($params, 'gate');
        
        switch ($gate) {
            case self::GATE_SWIFT_MAILER:

                $this->_mailer = new SwiftMailer();

                break;
            default :
                $this->_mailer = new SwiftMailer();
                break;
                
        }
        
        if ($this->_mailer==null) throw new Exception('Сервис Email не инициализирован');

        return $this->_mailer->send ($params ); 

    }
    
      function sendWithAttachment($params)
    {
        $gate=$this->getParam($params, 'gate');
        
        switch ($gate) {
            case self::GATE_SWIFT_MAILER:

                $this->_mailer = new SwiftMailer();

                break;
            default :
                $this->_mailer = new SwiftMailer();
                break;
                
        }
        
        if ($this->_mailer==null) throw new Exception('Сервис Email не инициализирован');

        return $this->_mailer->sendWithAttachment ($params ); 

    }
       
}		