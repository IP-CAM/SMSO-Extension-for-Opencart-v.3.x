<?php
class ModelExtensionSmsoHelper extends Model
{
    public function sendTestSMS($to, $body)
    {   	 
    	$token = $this->model_setting_setting->getSettingValue('smso_token');

     	if(!empty($token))
        {
	    	$smso = new ModelExtensionSmsoSmso($token);
	    	$sender = $this->getRandomSender($token);
	    	$to = $this->checkPhone($to);
	    	$response = $smso->sendMessage($to, $body, $sender);
	    	return $response;
    	}
    	return false;
    }
    public function getRandomSender($token)
    {
        $values = array();
        $senders_arr = array(
            0 => $this->model_setting_setting->getSettingValue('smso_sender')
        );
        if($token) 
        {
            $sms = new ModelExtensionSmsoSmso($token);
            $senders = $sms->getSenders();
            foreach ($senders['response'] as $s) {
                $values[] = $s['id'];
            }

            $senderii = array_intersect($senders_arr, $values);
            $rnd = rand(0,(count($senderii)-1));             
            return $senderii[$rnd];
        } else {
            return false;
        }
    }
    public function checkPhone($to) {
        if(strlen($to) == 10){
            $to = "+4".$to;
        }
        return $to;
    }
}