<?php

namespace common\services\notifications\services\freeswitch;

use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;
use \yii\base\Exception;

class FreeSwitchSender {
    use \common\services\notifications\traits\NService;
    
    private $server = 'fs.luxystech.com';
    private $port = 8787;
    private $pass = 'Kai1itheey4o';

    const TPL_COMMAND = "sofia/gateway/3162981c-a30c-45dc-b1ec-b1985a6b6c43/+7%s *709 XML krgmp-fs.luxystech.com";

    function send($params) {
        $to=$this->getParam($params, 'to');
        
        if (!$to)            throw new Exception("Недостаточно данных для работы FreeSwitch!");
        
        $client = new Client("/RPC2", $this->server, $this->port);
        $client->setCredentials("xmlrpc", $this->pass);

        $r = $client->send(new Request('freeswitch.api', array(new Value("originate", "string"), new Value(sprintf(self::TPL_COMMAND, $to), "string"))));

        $result = false;
        if (!$r->faultCode()) {
            $v = $r->value();
            $foo = $v->scalarval();

            $result = preg_match("/\+OK.+/", $foo) ? true : false;
        }

        return $result;
    }

}
