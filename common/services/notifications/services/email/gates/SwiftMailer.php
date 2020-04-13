<?php

namespace common\services\notifications\services\email\gates;

use \yii\base\Exception;
use Yii;

class SwiftMailer
{
    use \common\services\notifications\traits\NService;

    function send($params)
    {

        if (!\Yii::$app->has("mailer", false)) throw new Exception("Не подключен SwiftMailer");

        $from = $this->getParam($params, 'from');
        $message = $this->getParam($params, 'message');
        $subject = $this->getParam($params, 'subject');
        $to = $this->getParam($params, 'to');
        $compose_view = $this->getParam($params, 'compose_view');
        $compose_params = $this->getParam($params, 'compose_params');

        if($compose_params && $compose_view && $from && $subject && $to){
            return $this->sendWithView($compose_params, $compose_view, $from, $subject, $to);
        }

        if (!$from || !$message || !$subject || !$to) throw new Exception("Недостаточно данных для работы SwiftMailer");
        return \Yii::$app->mailer
            ->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setHtmlBody($message)
            ->send();
    }

    function sendWithAttachment($params)
    {

        if (!\Yii::$app->has("mailer", false))
            throw new Exception("Не подключен SwiftMailer");

        $from = $this->getParam($params, 'from');
        $message = $this->getParam($params, 'message');
        $subject = $this->getParam($params, 'subject');
        $to = $this->getParam($params, 'to');
        $attachment = $this->getParam($params, 'attachment');
        if (!$from || !$message || !$subject || !$to)
            throw new Exception("Недостаточно данных для работы SwiftMailer");
        $mail = \Yii::$app->mailer
            ->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setHtmlBody($message);

        if (is_array($attachment)) {
            foreach ($attachment as $attach) {
                $mail->attach($attach);
            }
        } else {
            $mail->attach($attachment);
        }
        return $mail->send();
    }

    /**
     * Отправка письма с представлением
     *
     * @param $compose_params  - параметры для представления
     * @param $compose_view    - путь к представлению
     * @param $from            - от кого
     * @param $subject         - тема письма
     * @param $to              - кому
     * @return bool
     */
    function sendWithView($compose_params, $compose_view, $from, $subject, $to)
    {
        return \Yii::$app->mailer
            ->compose($compose_view,
                $compose_params
            )
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }

}
 
