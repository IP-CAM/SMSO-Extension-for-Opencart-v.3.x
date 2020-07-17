<?php

class ControllerExtensionModuleSmso extends Controller {

    private $error = array();
    private $code = array('smso_test', 'smso');
    public  $smso_test_result = false;
    private $fields_test = array(
        "smso_test_phone_number" => array("label" => "Phone Number", "type" => "isPhoneNumber", "value" => "", "validate" => true),
    );
    private $fields = array(
        "smso_token"                    => array("label" => "Token", "type" => "isEmpty", "value" => "", "validate" => true),
        "smso_sender"                   => array("value" => ""),
        "smso_active"                   => array("value" => ""),

        "smso_canceled_active"          => array("value" => ""),
        "smso_canceled_message"         => array("value" => ""),

        "smso_canceled_reversal_active" => array("value" => ""),
        "smso_canceled_reversal_message"=> array("value" => ""),

        "smso_chargeback_active"        => array("value" => ""),
        "smso_chargeback_message"       => array("value" => ""),

        "smso_complete_active"          => array("value" => ""),
        "smso_complete_message"         => array("value" => ""),

        "smso_denied_active"            => array("value" => ""),
        "smso_denied_message"           => array("value" => ""),

        "smso_refunded_active"          => array("value" => ""),
        "smso_refunded_message"         => array("value" => ""),

        "smso_expired_active"           => array("value" => ""),
        "smso_expired_message"          => array("value" => ""),

        "smso_failed_active"            => array("value" => ""),
        "smso_failed_message"           => array("value" => ""),

        "smso_pending_active"           => array("value" => ""),
        "smso_pending_message"          => array("value" => ""),

        "smso_processed_active"         => array("value" => ""),
        "smso_processed_message"        => array("value" => ""),

        "smso_processing_active"        => array("value" => ""),
        "smso_processing_message"       => array("value" => ""),

        "smso_refunded_active"          => array("value" => ""),
        "smso_refunded_message"         => array("value" => ""),

        "smso_reversed_active"          => array("value" => ""),
        "smso_reversed_message"         => array("value" => ""),

        "smso_shipped_active"           => array("value" => ""),
        "smso_shipped_message"          => array("value" => ""),

        "smso_voided_active"            => array("value" => ""),
        "smso_voided_message"           => array("value" => ""),
    );

    public function index()
    {
        if(!$this->isModuleEnabled())
        {
            $this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']));
            exit;
        }

        $this->load->language('extension/module/smso');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle('/admin/view/stylesheet/smso/smso.css');

        $this->load->model('setting/setting');
        $this->load->model('setting/module');
        $this->load->model('design/layout');
        $this->load->model('extension/smso/validator');
        $this->load->model('extension/smso/smso');
        $this->load->model('extension/smso/helper');

        $this->submitted();
        $this->loadFieldsToData($data);
        $this->smso_sender_list = self::getValueSender($this->model_setting_setting->getSettingValue("smso_token"));
  
        $data['error_warning'] = $this->error;

        $data['smso_logo'] = '/admin/view/image/smso/logo.png';

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit']     = $this->language->get('text_edit');

        $data['btn_test_text']        = $this->language->get('btn_test_text');
        $data['btn_test_placeholder'] = $this->language->get('btn_test_placeholder');
        $data['btn_test_description'] = $this->language->get('btn_test_description');
        $data['btn_test_send']        =  $this->language->get('btn_test_send');

        $data['btn_token_text']        = $this->language->get('btn_token_text');
        $data['btn_token_placeholder'] = $this->language->get('btn_token_placeholder');
        $data['btn_token_description'] = $this->language->get('btn_token_description');

        $data['btn_token_save_all'] = $this->language->get('btn_token_save_all');

        $data['btn_status_order_description'] = $this->language->get('btn_status_order_description');

        $data['btn_status_order_canceled']          = $this->language->get('btn_status_order_canceled');
        $data['btn_status_order_canceled_reversal'] = $this->language->get('btn_status_order_canceled_reversal');
        $data['btn_status_order_chargebackd']       = $this->language->get('btn_status_order_chargebackd');
        $data['btn_status_order_complete']          = $this->language->get('btn_status_order_complete');
        $data['btn_status_order_denied']            = $this->language->get('btn_status_order_denied');
        $data['btn_status_order_expired']           = $this->language->get('btn_status_order_expired');
        $data['btn_status_order_failed']            = $this->language->get('btn_status_order_failed');
        $data['btn_status_order_pending']           = $this->language->get('btn_status_order_pending');
        $data['btn_status_order_processed']         = $this->language->get('btn_status_order_processed');
        $data['btn_status_order_processing']        = $this->language->get('btn_status_order_processing');
        $data['btn_status_order_refunded']          = $this->language->get('btn_status_order_refunded');
        $data['btn_status_order_reversed']          = $this->language->get('btn_status_order_reversed');
        $data['btn_status_order_shipped']           = $this->language->get('btn_status_order_shipped');
        $data['btn_status_order_voided']            = $this->language->get('btn_status_order_voided');

        $data['smso_sender_list']  = $this->smso_sender_list;
        $data['order_status_list'] = $this->order_status_list;
        $data['smso_test_result']  = $this->smso_test_result;

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/smso', $data));
    }
    public function isModuleEnabled()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "extension WHERE code = 'smso'";
        $result = $this->db->query($sql);
        if($result->num_rows)
        {
            return true;
        }
        return false;
    }
	public function submitted()
    {		
        if(!empty($_POST))
        {
            if(!empty($_POST['smso_test']))
            {                 
                $this->validateFields(); 
                if(empty($_POST['smso_token']))
                {
                    $this->error[] = array("error" => "Field token is required for testing.");
                }

                if(empty($this->error))
                {
                    $this->saveFiledsToDB();
                    $fields = $this->getFieldsValue();

                    $message = 'THIS TEST WAS SEND FROM SMSO OPENCART EXTENSION!';
                    $result = $this->model_extension_smso_helper->sendTestSMS($fields['smso_test_phone_number']['value'], $message);

                    if(isset($result['response']['status']) && $result['response']['status'] == "200")
                    {
                    	$this->smso_test_result = true;
                    }
                }
            }
            else {
            	
                $this->validateFields();
                if(empty($this->error))
                {
                    $this->saveFiledsToDB();
                }
            }
            return true;
        }
        return false;
	}
    public function loadFieldsToData(&$data)
    {
        foreach ($this->fields as $key => $value) 
        {                
            $data[$key] = $this->model_setting_setting->getSettingValue($key);
        }

        foreach ($this->fields_test as $key => $value) 
        {                
            $data[$key] = $this->model_setting_setting->getSettingValue($key);
        }
    }
    public function saveFiledsToDB()
    {
        $fields = $this->getPostFiles();

        foreach (array_keys($fields) as $key) 
        {        
            if(isset($_POST[$key]))
            {        
               $fields[$key] = $_POST[$key];
            }
            else {
               $fields[$key] = "";   
            }
        }

        if(empty($_POST['smso_test'])){
            $module_fields = array();
            if($fields['smso_active']){
                $module_fields['module_smso_status'] = 'true';
            }
            else{
                $module_fields['module_smso_status'] = 'false';
            }
            $this->model_setting_setting->editSetting("module_smso", $module_fields);
        }

        $this->model_setting_setting->editSetting($this->getCode(), $fields);
    }
    public function validateFields()
    {
        $fields = $this->getPostFiles();

        foreach ($fields as $key => $value) 
        {                
            // if(isset($value['validate']) && $value['validate'] && empty($_POST[$key]))
            if(isset($value['validate']))
            {
                $result = call_user_func_array(array($this->model_extension_smso_validator, $value['type']), array($_POST[$key]));
                if(!$result)
                {
                    $this->error[] = array("error" => "Field ".$value['label']." is required for testing.");
                }
            }
        }
    }
    public function getFieldsValue()
    {
        $fields = $this->getPostFiles();

        foreach ($fields as $key => $value) 
        {                
            $fields[$key]["value"] = $this->model_setting_setting->getSettingValue($key);
        }
        return $fields;
    }
    public function getPostFiles()
    {
        return (!empty($_POST['smso_test']) ? $this->fields_test : $this->fields);
    }
    public function getCode()
    {
        return (!empty($_POST['smso_test']) ? $this->code[0] : $this->code[1]);
    }
    public static function getValueSender($token)
    {
        $values = array();        
        if( $token != '') {
            $smso = new ModelExtensionSmsoSmso($token); 
            $senders = $smso->getSenders();
            if(!empty($senders))
                foreach ($senders['response'] as $s) {
                    $values[] = array(
                        'id' => $s['id'],
                        'label' => $s['name']."(cost:".$s['pricePerMessage'].")",
                        'value' => $s['id'],
                    );
                }
            else{
                $values = false;
            }
        }
        return $values;
    }
    public function install()
    {
        $this->load->model('setting/event');
        $this->model_setting_event->addEvent('smso', 'catalog/model/checkout/order/addOrderHistory/before', 'extension/module/smso/status_change');
    }
    public function uninstall()
    {

    }
}