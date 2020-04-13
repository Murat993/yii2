<?php
namespace common\services\notifications\traits;

trait NService {
    
      
    public function getParam($params,$name)
    {
         if (!empty($params) && is_array($params))
         {
             if (!empty($params[$name]))
             {
                 return $params[$name];
             }
         }
         return null;
    }
    
    /**
     * Генерируем сообщение из шаблона и данных
     * @param type $template
     * @param type $data
     * @return type
     */
    public  function getMessage($template,$data=[])
    {
            $text=$template;
            if (is_array($data) && count($data)>0)
            {
                $arr=[];
                $arr1=[];
                foreach ($data as $key=>$value) {
                    $arr[]=$key;
                    $arr1[]=$value;
                }
                $text = str_replace($arr, $arr1, $template);
            }
            return $text;
    }
    
    public function getResultArray( $result , $text , $subject = null ){
            if ($result === true) {
                     return [
                        'status'=> 'success',
                        'message' => $text,
                        'subject'=> $subject,
                       ];
            }
            else {
                    return [
                        'status' => 'error',
                    ] ;
            }
    }
    
    
    /*
     * обрезаем номер сотового телефона
     */
    function cutCellPhoneNumber($phone) {
            $phone = preg_replace ( '/[^0-9]/', '', $phone );
            if (strlen ( $phone ) > 10) {
                    $phone = substr ( $phone, - 10, 11 );
            }
            return $phone;
    }
       
    /**
     * првоеряем нмоер городской или нет
     * @param type $phone
     * @return boolean
     */
    public function isCityNumber($phone)
    {
        $prefix=  substr($this->cutCellPhoneNumber($phone), 0, 3);
        if (in_array($prefix, [700,708,778,702,701,705,771,776,777,707,747,771])) return false;


        return true;
    }
    
    /**
     * получаем цену за смс в зависимости от оператора
     * @param type $phone
     * @return int
     */
    public function getCost($phones)
    {
        $cost=0;
        
        if (is_array($phones))
        {
            foreach ($phones as $phone) {
                $cost+=$this->getSmsCost($phone);
            }
        }else
        {
            $cost+=$this->getSmsCost($phones);
        }
        return $cost;
    }

    /**
     * стоимость смс на списание со счета
     * @param type $phone
     * @return int
     */
    public function getSmsCost($phone)
    {
        if ($this->isCityNumber($phone)) return 7;
        
        $prefix=  substr($phone, 0, 3);
        switch ($prefix) {
            case 700:  // алтел
            case 708:
                return 6;
            case 778: // кисель и актив
            case 702:

            case 701:
               return 6;
            case 705:  // билаин
            case 771:
            case 776:
            case 777:
                return 5;
            case 707:  // теле 2
            case 747:
            case 771:
                return 4;

            default:
                return 6;
        }
    }
    
    
       
}