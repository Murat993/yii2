<?php
namespace common\services\notifications\services\push\gates;

class FirebasePushMessage {
	protected  $url = 'https://fcm.googleapis.com/fcm/send';
	protected  $devices =[];
	
	protected $api_key='AAAA5_k5R8E:APA91bFa1YWMe5jZ2K1o3uyLEp4aSIip2UZMAwBSpAjX9EI2dk7n2F5qgBQLvUZw0D-bdQyO6tSs30itfoXKMLxT6PjmKm21o0AqwiocReIYOKoQw9YtmVXwzW1S4MKh8cB4Q6DGtI4F';
	
	function setDevices($deviceIds){
		if(is_array($deviceIds)){
			$this->devices = $deviceIds;
		} else {
			$this->devices = array($deviceIds);
		}
	}
	
	
	/*
	 Send the message to the device
	 @param $message The message to send
	 @param $data Array of data to accompany the message
	 */
    function send($message, $data = false){
        if(!is_array($this->devices) || count($this->devices) == 0){
            $this->error("No devices set");
        }

        $msg = [
            'body'  => $message,
            'title'     => $data['subject'],
            'vibrate'   => 1,
            'sound'     => 1,
        ];

        $fields = [
            'registration_ids'  => $this->devices,
            'notification'      => $msg
        ];

//		$fields = array(
//				'to'  => reset($this->devices),
//				'data' => array( "message" => $message ),
//		);
//
//		if(is_array($data)){
//			foreach ($data as $key => $value) {
//				$fields['data'][$key] = $value;
//			}
//		}
//
        $headers = [
            'Authorization: key='.$this->api_key,
            'Content-Type: application/json'
        ];

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $this->url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Avoids problem with https certificate
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
        print_r($result);
        return $result;
    }

}
