<?php 
namespace common\services\notifications\services\sms\gates;
use \yii\base\Exception;

class Smsckz  {
    use \common\services\notifications\traits\NService;
    
    //const PHONE_PREFIX='7';
    protected $login='washmap.ru';
    protected $password='washmap01';
            
    function send($params) // посылаем смску
    {
        $to=$this->getParam($params, 'phones');
        $text=$this->getParam($params, 'message');
        
        if (!$to || !$text)            throw new Exception("Недостаточно данных для работы SmsKz!");
        
        $phones='';
        if (is_array($to))
        {
            $i=0;
            foreach ($to as $phone) {
                $phones.=$phone;//self::PHONE_PREFIX.$this->cutCellPhoneNumber($phone);
                if ($i<count($to))
                {
                    $phones.=',';
                }
                $i++;
            }
        }else
        {
            $phones=$to;//self::PHONE_PREFIX.$this->cutCellPhoneNumber($to);
        }

        $arContext['http']['timeout'] = 10;
        $context = stream_context_create($arContext);

        $result= @file_get_contents ( "https://smsc.kz/sys/send.php?login={$this->login}&sender=myplaces.me&psw={$this->password}&cost=3&charset=utf-8&fmt=3&phones={$phones}&mes=". rawurlencode($text),false,$context );          

        $data=json_decode($result);
        
        if (empty($data->id)) throw new Exception('Ошибка при отправке смс ');
        
        return [
             'result'=>'OK',
             'sms_cost'=>!empty($data->cost)?$data->cost:'Не определено',
             'invoice_sms_cost'=>$this->getCost($phones),
             'sms_id'=>!empty($data->id)?$data->id:'Не определено',
             'to'=>$phones,
             'message'=>$text,
            ];
    }
    

        
        
    
    
}
// сообщение передано
//stdClass Object
//(
//    [id] => 3218
//    [cnt] => 1
//    [cost] => 3.3
//    [balance] => 3215.3097
//)

// ошибка
//stdClass Object
//(
//    [error] => duplicate request, wait a minute
//    [error_code] => 9
//)