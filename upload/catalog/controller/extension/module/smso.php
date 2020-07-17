<?php
class ControllerExtensionModuleSmso extends Controller
{

    public function custom_field()
    {
        var_dump("dddddddddddddddd");
        exit;
    }
    public function status_change($route, $data)
    {
    	$orderStatusId = $data[1];
        $orderId = $data[0];

        $this->load->model('setting/setting');
        $this->load->model('checkout/order');
        $this->load->model('extension/smso/order');
        $this->load->model('extension/smso/helper');
        $this->load->model('extension/smso/smso');

        $order = $this->model_checkout_order->getOrder($orderId); 
        $status_name = $this->model_extension_smso_order->getStatusName($orderStatusId);
        $smso_activate = $this->model_setting_setting->getSettingValue("smso_active");

        if($this->isModuleEnabled() && !empty($smso_activate) && !empty($status_name))
        {
        	$status_name = str_replace(" ", "_", $status_name);
        	$status_activate = $this->model_setting_setting->getSettingValue("smso_" . strtolower($status_name) . "_active");
        	$status_message  = $this->model_setting_setting->getSettingValue("smso_" . strtolower($status_name) . "_message");
 			
        	if(!empty($status_activate) && !empty($status_message))
        	{
        		$replace = array(
	                '{order_number}'       => $order['order_id'],
	                '{order_date}'         => $order['date_added'],
	                '{order_total}'        => round($order['total']*$order['currency_value'], 2).' '.$order['currency_code'],
	                '{billing_first_name}' => $order['payment_firstname'],
	                '{billing_last_name}'  => $order['payment_lastname'],
	                '{shipping_method}'    => $order['shipping_method'],
	            );

	            foreach ($replace as $key => $value) {
	                $status_message = str_replace($key, $value, $status_message);
	            }

                $result = $this->model_extension_smso_helper->sendTestSMS($order['telephone'], $status_message);
        	}
        }
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

}