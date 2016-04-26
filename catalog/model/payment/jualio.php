<?php 
class ModelPaymentJualio extends Model {
  	public function getMethod($address, $total) {
			$this->load->language('payment/jualio');
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('jualio_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if ($this->config->get('jualio_total') > $total) {
				$status = false;
			} elseif (!$this->config->get('jualio_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}	

			$currencies = array(
				'AUD',
				'CAD',
				'EUR',
				'GBP',
				'JPY',
				'USD',
				'NZD',
				'CHF',
				'HKD',
				'SGD',
				'SEK',
				'DKK',
				'PLN',
				'NOK',
				'HUF',
				'CZK',
				'ILS',
				'MXN',
				'MYR',
				'BRL',
				'PHP',
				'TWD',
				'THB',
				'IDR',
				'TRY'
			);
			
			if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
				$status = false;
			}			
						
			$method_data = array();

			$jualio_title = $this->config->get('jualio_title');
			if ($this->config->get('jualio_title') == '') {
				$jualio_title = 'Jualio';
			}
		
			if ($status) {  
	    		$method_data = array( 
	      		'code'       => 'jualio',
	      		'title'      => $jualio_title,
						'sort_order' => $this->config->get('jualio_sort_order')
	    		);
	  	}
   
    	return $method_data;
  	}
}
?>