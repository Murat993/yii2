<?php 
namespace common\services\notifications\services\voice\gates;
use \yii\base\Exception;

class Smsckz  {
    use \common\services\notifications\traits\NService;
    
    const PHONE_PREFIX='7';
    protected $login='washmap.ru';
    protected $password='washmap01';
            
    function send($params) // посылаем смску
    {
        $to=$this->getParam($params, 'phone');
        $text=$this->getParam($params, 'message');
        
        if (!$to || !$text)            throw new Exception("Недостаточно данных для работы SmsKz!");
        
        if (is_array($to)) throw new Exception("Массив не разрешен!");
        
        if (!$this->isCityNumber($to)) throw new Exception("Номер не является городским");

        $phone=self::PHONE_PREFIX.$this->cutCellPhoneNumber($to);

        $arContext['http']['timeout'] = 10;
        $context = stream_context_create($arContext);

        $result=@file_get_contents ( "https://smsc.kz/sys/send.php?login={$this->login}&psw={$this->password}&charset=utf-8&phones={$phone}&mes=".rawurlencode($text." оооо")."&call=1&voice=w2",false,$context );  
        
        return [
                'result'=>'OK',
                'invoice_sms_cost'=>$this->getCost($phone),
                'to'=>$phone,
                'message'=>$text,
        ];
    }
    
}