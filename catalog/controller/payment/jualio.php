<?php
class ControllerPaymentJualio extends Controller {
	public function index() {
		$this->language->load('payment/jualio');
		
		$this->data['text_testmode'] = $this->language->get('text_testmode');		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['testmode'] = $this->config->get('jualio_test');
		
		if (!$this->config->get('jualio_test')) {
    		$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$this->data['business'] = $this->config->get('jualio_email');
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');				
			
			$this->data['products'] = array();
			
			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				$description_string = $product['name'];
	
				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);
						
						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
					}
										
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);

					$description_string = $description_string.' '.$option['name'].':'.(utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value).' ';
				}

				// original product data
				// $product_data = array(
				// 	'name'     => $product['name'],
				// 	'model'    => $product['model'],
				// 	'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
				// 	'quantity' => $product['quantity'],
				// 	'option'   => $option_data,
				// 	'weight'   => $product['weight']
				// );

				// modified product data
				$product_data = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false) * $product['quantity'],
					'quantity' => 1,
					'option'   => $option_data,
					'weight'   => $product['weight']
				);

				$product_data['description'] = $description_string;

				if ($product['image'] != '') {
					$product_data['image'] = $this->config->get('config_url').'image/'.$product['image'];
				} else {
					$product_data['image'] = 'https://i.jual.io/no-image.png';
				}
				
				$this->data['products'][] = $product_data;
			}	
			
			$this->data['discount_amount_cart'] = 0;
			
			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'description'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0,
					'image'    => 'https://i.jual.io/no-image.png'
				);	
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}
			
			$this->data['currency_code'] = $order_info['currency_code'];
			$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
			$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
			$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
			$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
			$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');	
			$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');	
			$this->data['telephone'] = $this->customer->getTelephone();	
			$this->data['country'] = $order_info['payment_iso_code_2'];
			$this->data['email'] = $order_info['email'];
			$this->data['invoice'] = $this->session->data['order_id']; // JUALIO
			$this->data['lc'] = $this->session->data['language'];
			$this->data['return'] = $this->url->link('checkout/success');
			$this->data['notify_url'] = $this->url->link('payment/jualio/notify', '', 'SSL'); // JUALIO
			$this->data['callback_url'] = $this->url->link('payment/jualio/callback', '', 'SSL'); // JUALIO
			$this->data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');

			// $this->data['callback_url'] = $this->url->link('checkout/success'); // TEST 2
			
			if (!$this->config->get('jualio_transaction')) {
				$this->data['paymentaction'] = 'authorization';
			} else {
				$this->data['paymentaction'] = 'sale';
			}
			
			$this->data['custom'] = $this->encryption->encrypt($this->session->data['order_id']);
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/jualio.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/jualio.tpl';
			} else {
				$this->template = 'default/template/payment/jualio.tpl';
			}

			// JUALIO
			$this->data['jualio_customer_key'] = $this->config->get('jualio_customer_key');
			$this->data['jualio_client_id'] = $this->config->get('jualio_client_id');
			if ($this->config->get('jualio_test')) {
				$this->data['jualio_url'] = 'http://dev.app.jualio.com/client/v2/payments/actions/create';
			} else {
				$this->data['jualio_url'] = 'https://app.jualio.com/client/v2/payments/actions/create';
			}
			$this->data['jualio_payment_channel_type'] = 'bank_transfer';
			$this->data['jualio_payment_channel_direct'] = 'TRUE';
			$this->data['orderid'] = date('His') . $this->session->data['order_id'];

			// echo '<pre>';
			// print_r($this->customer->get('telephone'));
			// echo '</pre>';

			$this->render();
		}
	}
	
	public function notify() {
		$input = json_decode(file_get_contents("php://input"));

		if ($input->object == 'transaction_notify' && $input->transaction->status == 'SUCCESS') {

			$order_id = $input->transaction->invoice_no;

			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder(intval($order_id));

			if ($order_info) {

				$order_status_id = $this->config->get('jualio_completed_status_id');

				if ($order_info->$order_status_id) {
					$this->model_checkout_order->confirm($order_id, $order_status_id);
				} else {
					$this->model_checkout_order->update($order_id, $order_status_id);
				}

				header('Content-Type: application/json');
				header('X-PHP-Response-Code: 200', true, 200);

				// DEBUG here
				// $return_json = json_encode($order_info);
				// echo $return_json;
			}
		}
	}

	public function callback() {

		if (isset($this->request->post['orderid'])) {
			$order_id = trim(substr(($this->request->post['orderid']), 6));
		} else {
			$order_id = 0;
		}

		$return = array(
			'status' => 'success',
			'order_id' => $orderid
		);
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);

    if ($order_info) {
      $data = array_merge($this->request->post,$this->request->get);
 
      $return_url = $this->config->get('config_url').'index.php?route=checkout/success';

      //payment was made successfully
      if ($data['status'] == 'SUCCESS' || $data['status'] == 'y') {
        // update the order status accordingly
        $order_status_id = $this->config->get('jualio_completed_status_id');
        if ($this->config->get('jualio_success_return')) {
        	$return_url = $this->config->get('jualio_success_return');
        } else {
        	$return_url = $this->url->link('checkout/success');
        }

      } else {
      	$order_status_id = $this->config->get('jualio_failed_status_id');
        if ($this->config->get('jualio_fail_return')) {
        	$return_url = $this->config->get('jualio_fail_return');
        } else {
        	$return_url = $this->url->link('checkout/checkout');
        }
      }

			if (!$order_info->$order_status_id) {
				$this->model_checkout_order->confirm($order_id, $order_status_id);
			} else {
				$this->model_checkout_order->update($order_id, $order_status_id);
			}

      $this->model_checkout_order->confirm($order_id, $order_status_id);

      header('Location: '.$return_url);
    }


		// Jualio gives a GET request while we need a POST, so create a form and send using javascript
		if (isset($this->request->get['orderid'])) {
			echo '<form method="POST" id="jualio-submit" action="?route='.$_GET['route'].'">';
			foreach ($_GET as $key => $value) {
				echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
			}
			echo '<button type="submit">Click if you are not redirected automatically</button>';
			echo '</form>';
			echo '<script type="text/javascript">console.log("hello"); document.getElementById("jualio-submit").submit(); </script>';
		}

	}
}
?>
