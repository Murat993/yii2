<?php
namespace common\services\notifications\services\voice;

use common\services\notifications\services\voice\gates\Smsckz;
use \yii\base\Exception;

class VoiceSender  {
    use \common\services\notifications\traits\NService;
    
    const GATE_SMSCKZ='SmscKz';
    
    private $_voicer;
    
    
    function send($params)
    {
        $gate=$this->getParam($params, 'gate');
        
        switch ($gate) {
            case self::GATE_SMSCKZ:

                $this->_voicer = new Smsckz();

                break;
            default :
                $this->_voicer = new Smsckz();
                break;
                
        }
        
        if ($this->_voicer==null) throw new Exception('Сервис голосовых сообщений не инициализирован');

        return $this->_voicer->send ($params ); 

    }
    
    
       
}		