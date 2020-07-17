<?php
class ModelExtensionSmsoOrder extends Model {

	public function getStatusName($statusId)
	{
		$order_status = $this->db->query("SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = " . $statusId . " AND os.language_id = 1");
		if($order_status->num_rows)
		{
			return $order_status->row['name'];
		}
		return false;
	}
}