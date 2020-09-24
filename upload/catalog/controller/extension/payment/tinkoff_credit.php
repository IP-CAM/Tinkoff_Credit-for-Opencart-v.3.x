<?php
class ControllerExtensionPaymentTinkoffCredit extends Controller {
	public function index() {
		$this->load->language('extension/payment/tinkoff_credit');

		$data['action'] = $this->config->get('payment_tinkoff_credit_action');
        $data['shopId'] = $this->config->get('payment_tinkoff_credit_shopId');
        $data['showcaseId'] = $this->config->get('payment_tinkoff_credit_showcaseId');

        $data['promoCode'] = array();


        $data['phone'] = $this->session->data['guest']['telephone'];
        $data['email'] = $this->session->data['guest']['email'];

        $data['products'] = array();
        $products = $this->cart->getProducts();

        $this->load->model('tool/image');
        $this->load->model('tool/upload');

        $data['products'] = array();

        $i = 0;

        foreach ($products as $product) {

            // TOTALS COUPON FOR TINKOFF CREDIT

            /*if(isset($this->session->data['coupon'])) {
                $coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);
            }
            if(isset($coupon_info)) {
                if ($coupon_info['type'] == 'P') {
                    $discount = 1 - (1 / 100 * $coupon_info['discount']);
                }elseif ($coupon_info['type'] == 'F') {
                    $discount = 1; // Внимание, функция не работает для непроцентной скидки!
                }
            }else{
                $discount = 1;
            }

            if($product['special'] == 1){
                $discount = 1;
            }*/



            //end

            $data['products'][] = array(
                'i'          => $i,
                'name'       => $product['name'],
                'model'      => $product['model'],
                'quantity'   => $product['quantity'],
                'final'      => $product['price']/* * $discount */,
            );

            $i++;
        }

        $order_data = array();

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $order_data['totals'] = $totals;

        $data['totals'] = array();

        foreach ($order_data['totals'] as $total) {
            $data['totals'][] = array(
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
                'value' => $total['value']
            );
        }

        $data['sum'] = $data['totals'][array_key_last($data['totals'])]['value'];

		return $this->load->view('extension/payment/tinkoff_credit', $data);
	}

	public function confirm() {
		$json = array();
		
		if ($this->session->data['payment_method']['code'] == 'tinkoff_credit') {
			$this->load->language('extension/payment/tinkoff_credit');

			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_tinkoff_credit_order_status_id'), true);
		
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}