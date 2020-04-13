<?php
namespace common\services\notifications\services\push;


use common\services\notifications\services\push\gates\FirebasePushMessage;
use \yii\base\Exception;

class PushSender   {
    use \common\services\notifications\traits\NService;
    
    const GOOGLE_API_KEY='AIzaSyCvZTqzvQqkiyuZ3JfYSJ9kLKDdLswBq4E';
    
    const GATE_ANDROID=1;
    const GATE_IOS=2;
    
    private $_push;
    

    function send($params)
    {
        $gate=$this->getParam($params, 'gate');
        
        switch ($gate) {
            case self::GATE_ANDROID:
                return $this->sendAndroid($params);
            case self::GATE_IOS:
                return $this->sendAndroid($params);


            default :
                return $this->sendAndroid($params);
        }
        

    }
    
    public function sendAndroid($params)
    {
            $to=$this->getParam($params, 'tokens');
            unset($params['tokens']);
            $text=$this->getParam($params, 'message');
            unset($params['message']);
            if (!$to || !$text)            {
                return;
            }
            $data=[];
            if (count($params)>0){
                foreach ($params as $key=>$value) {
                    $data[$key]=$value;
                }
            }
            $this->_push = new FirebasePushMessage();
            $this->_push->setDevices($to);

            return $this->_push->send($text, $data);
    }
    
    public function sendIos($params)
    {
        if (!\Yii::$app->has ( "apns", false )) throw new Exception("Не подключен apns");
        $apns = \Yii::$app->apns;
        
        $push_tokens=$this->getParam($params, 'tokens');
        $message=$this->getParam($params, 'message');
        $order_type=$this->getParam($params, 'order_type');
        
        if (!$push_tokens || !$message )      {
            return;
        }   //   throw new Exception("Недостаточно данных для работы push!");

        return @$apns->send($push_tokens, $message,
          [
              'order_type' => $order_type,
//            'customProperty_2' => 'World'
          ],
          [
            'sound' => 'default',
            'badge' => 1
          ]
        );
    }
    
    
       
}
