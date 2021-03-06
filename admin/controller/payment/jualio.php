<?php
class ControllerPaymentJualio extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/jualio');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('jualio', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_authorization'] = $this->language->get('text_authorization');
		$this->data['text_sale'] = $this->language->get('text_sale');

		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_transaction'] = $this->language->get('entry_transaction');
		$this->data['entry_debug'] = $this->language->get('entry_debug');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$this->data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$this->data['entry_denied_status'] = $this->language->get('entry_denied_status');
		$this->data['entry_expired_status'] = $this->language->get('entry_expired_status');
		$this->data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$this->data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$this->data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$this->data['entry_refunded_status'] = $this->language->get('entry_refunded_status');
		$this->data['entry_reversed_status'] = $this->language->get('entry_reversed_status');
		$this->data['entry_voided_status'] = $this->language->get('entry_voided_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		// JUALIO 
		$this->data['entry_client_id'] = $this->language->get('entry_client_id');
		$this->data['entry_customer_key'] = $this->language->get('entry_customer_key');
		$this->data['entry_success_return'] = $this->language->get('entry_success_return');
		$this->data['entry_fail_return'] = $this->language->get('entry_fail_return');


		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = '';
		}


		$this->data['breadcrumbs'] = array();

 		$this->data['breadcrumbs'][] = array(
   		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
  		'separator' => false
 		);

 		$this->data['breadcrumbs'][] = array(
   		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
  		'separator' => ' :: '
 		);

 		$this->data['breadcrumbs'][] = array(
   		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/jualio', 'token=' . $this->session->data['token'], 'SSL'),
  		'separator' => ' :: '
 		);

		$this->data['action'] = $this->url->link('payment/jualio', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');


		if (isset($this->request->post['jualio_title'])) {
				$this->data['jualio_title'] = $this->request->post['jualio_title'];
		} else {
			$this->data['jualio_title'] = $this->config->get('jualio_title');
		}

		if (isset($this->request->post['jualio_email'])) {
			$this->data['jualio_email'] = $this->request->post['jualio_email'];
		} else {
			$this->data['jualio_email'] = $this->config->get('jualio_email');
		}

		if (isset($this->request->post['jualio_test'])) {
			$this->data['jualio_test'] = $this->request->post['jualio_test'];
		} else {
			$this->data['jualio_test'] = $this->config->get('jualio_test');
		}

		if (isset($this->request->post['jualio_transaction'])) {
			$this->data['jualio_transaction'] = $this->request->post['jualio_transaction'];
		} else {
			$this->data['jualio_transaction'] = $this->config->get('jualio_transaction');
		}

		if (isset($this->request->post['jualio_debug'])) {
			$this->data['jualio_debug'] = $this->request->post['jualio_debug'];
		} else {
			$this->data['jualio_debug'] = $this->config->get('jualio_debug');
		}
		
		if (isset($this->request->post['jualio_total'])) {
			$this->data['jualio_total'] = $this->request->post['jualio_total'];
		} else {
			$this->data['jualio_total'] = $this->config->get('jualio_total'); 
		} 

		//JUALIO
		if (isset($this->request->post['jualio_customer_key'])) {
			$this->data['jualio_customer_key'] = $this->request->post['jualio_customer_key'];
		} else {
			$this->data['jualio_customer_key'] = $this->config->get('jualio_customer_key'); 
		}
		if (isset($this->request->post['jualio_client_id'])) {
			$this->data['jualio_client_id'] = $this->request->post['jualio_client_id'];
		} else {
			$this->data['jualio_client_id'] = $this->config->get('jualio_client_id'); 
		}
		if (isset($this->request->post['jualio_success_return'])) {
			$this->data['jualio_success_return'] = $this->request->post['jualio_success_return'];
		} else {
			$this->data['jualio_success_return'] = $this->config->get('jualio_success_return'); 
		}
		if (isset($this->request->post['jualio_fail_return'])) {
			$this->data['jualio_fail_return'] = $this->request->post['jualio_fail_return'];
		} else {
			$this->data['jualio_fail_return'] = $this->config->get('jualio_fail_return'); 
		}

		if (isset($this->request->post['jualio_canceled_reversal_status_id'])) {
			$this->data['jualio_canceled_reversal_status_id'] = $this->request->post['jualio_canceled_reversal_status_id'];
		} else {
			$this->data['jualio_canceled_reversal_status_id'] = $this->config->get('jualio_canceled_reversal_status_id');
		}
		
		if (isset($this->request->post['jualio_completed_status_id'])) {
			$this->data['jualio_completed_status_id'] = $this->request->post['jualio_completed_status_id'];
		} else {
			$this->data['jualio_completed_status_id'] = $this->config->get('jualio_completed_status_id');
		}	
		
		if (isset($this->request->post['jualio_denied_status_id'])) {
			$this->data['jualio_denied_status_id'] = $this->request->post['jualio_denied_status_id'];
		} else {
			$this->data['jualio_denied_status_id'] = $this->config->get('jualio_denied_status_id');
		}
		
		if (isset($this->request->post['jualio_expired_status_id'])) {
			$this->data['jualio_expired_status_id'] = $this->request->post['jualio_expired_status_id'];
		} else {
			$this->data['jualio_expired_status_id'] = $this->config->get('jualio_expired_status_id');
		}
				
		if (isset($this->request->post['jualio_failed_status_id'])) {
			$this->data['jualio_failed_status_id'] = $this->request->post['jualio_failed_status_id'];
		} else {
			$this->data['jualio_failed_status_id'] = $this->config->get('jualio_failed_status_id');
		}	
								
		if (isset($this->request->post['jualio_pending_status_id'])) {
			$this->data['jualio_pending_status_id'] = $this->request->post['jualio_pending_status_id'];
		} else {
			$this->data['jualio_pending_status_id'] = $this->config->get('jualio_pending_status_id');
		}
									
		if (isset($this->request->post['jualio_processed_status_id'])) {
			$this->data['jualio_processed_status_id'] = $this->request->post['jualio_processed_status_id'];
		} else {
			$this->data['jualio_processed_status_id'] = $this->config->get('jualio_processed_status_id');
		}

		if (isset($this->request->post['jualio_refunded_status_id'])) {
			$this->data['jualio_refunded_status_id'] = $this->request->post['jualio_refunded_status_id'];
		} else {
			$this->data['jualio_refunded_status_id'] = $this->config->get('jualio_refunded_status_id');
		}

		if (isset($this->request->post['jualio_reversed_status_id'])) {
			$this->data['jualio_reversed_status_id'] = $this->request->post['jualio_reversed_status_id'];
		} else {
			$this->data['jualio_reversed_status_id'] = $this->config->get('jualio_reversed_status_id');
		}

		if (isset($this->request->post['jualio_voided_status_id'])) {
			$this->data['jualio_voided_status_id'] = $this->request->post['jualio_voided_status_id'];
		} else {
			$this->data['jualio_voided_status_id'] = $this->config->get('jualio_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['jualio_geo_zone_id'])) {
			$this->data['jualio_geo_zone_id'] = $this->request->post['jualio_geo_zone_id'];
		} else {
			$this->data['jualio_geo_zone_id'] = $this->config->get('jualio_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['jualio_status'])) {
			$this->data['jualio_status'] = $this->request->post['jualio_status'];
		} else {
			$this->data['jualio_status'] = $this->config->get('jualio_status');
		}
		
		if (isset($this->request->post['jualio_sort_order'])) {
			$this->data['jualio_sort_order'] = $this->request->post['jualio_sort_order'];
		} else {
			$this->data['jualio_sort_order'] = $this->config->get('jualio_sort_order');
		}

		$this->template = 'payment/jualio.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/jualio')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['jualio_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		// if (!$this->request->post['jualio_title']) {
		// 	$this->error['title'] = $this->language->get('error_title');
		// }

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>