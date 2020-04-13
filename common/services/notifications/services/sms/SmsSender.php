<?php
namespace common\services\notifications\services\sms;

use common\services\notifications\services\sms\gates\Smsckz;
use \yii\base\Exception;

class SmsSender  {
    use \common\services\notifications\traits\NService;
    
    const GATE_SMSCKZ='SmscKz';
    
    private $_smser;
    
    
    function send($params)
    {
        $gate=$this->getParam($params, 'gate');
        
        switch ($gate) {
            case self::GATE_SMSCKZ:

                $this->_smser = new Smsckz();

                break;
            default :
                $this->_smser = new Smsckz();
                break;
                
        }
        
        if ($this->_smser==null) throw new Exception('Сервис sms не инициализирован');

        return $this->_smser->send ($params ); 

    }
    
    
       
}		