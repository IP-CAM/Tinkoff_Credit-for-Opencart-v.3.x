<?php
class ControllerExtensionPaymentTinkoffCredit extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/tinkoff_credit');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_tinkoff_credit', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/tinkoff_credit', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/tinkoff_credit', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		$this->load->model('localisation/language');

		$data['payment_tinkoff_credit'] = array();

        if (isset($this->request->post['payment_tinkoff_credit_shopId'])) {
            $data['payment_tinkoff_credit_shopId'] = $this->request->post['payment_tinkoff_credit_shopId'];
        } else {
            $data['payment_tinkoff_credit_shopId'] = $this->config->get('payment_tinkoff_credit_shopId');
        }

        if (isset($this->request->post['payment_tinkoff_credit_showcaseId'])) {
            $data['payment_tinkoff_credit_showcaseId'] = $this->request->post['payment_tinkoff_credit_showcaseId'];
        } else {
            $data['payment_tinkoff_credit_showcaseId'] = $this->config->get('payment_tinkoff_credit_showcaseId');
        }
		


		if (isset($this->request->post['payment_tinkoff_credit_total'])) {
			$data['payment_tinkoff_credit_total'] = $this->request->post['payment_tinkoff_credit_total'];
		} else {
			$data['payment_tinkoff_credit_total'] = $this->config->get('payment_tinkoff_credit_total');
		}

		if (isset($this->request->post['payment_tinkoff_credit_order_status_id'])) {
			$data['payment_tinkoff_credit_order_status_id'] = $this->request->post['payment_tinkoff_credit_order_status_id'];
		} else {
			$data['payment_tinkoff_credit_order_status_id'] = $this->config->get('payment_tinkoff_credit_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_tinkoff_credit_geo_zone_id'])) {
			$data['payment_tinkoff_credit_geo_zone_id'] = $this->request->post['payment_tinkoff_credit_geo_zone_id'];
		} else {
			$data['payment_tinkoff_credit_geo_zone_id'] = $this->config->get('payment_tinkoff_credit_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_tinkoff_credit_status'])) {
			$data['payment_tinkoff_credit_status'] = $this->request->post['payment_tinkoff_credit_status'];
		} else {
			$data['payment_tinkoff_credit_status'] = $this->config->get('payment_tinkoff_credit_status');
		}

		if (isset($this->request->post['payment_tinkoff_credit_sort_order'])) {
			$data['payment_tinkoff_credit_sort_order'] = $this->request->post['payment_tinkoff_credit_sort_order'];
		} else {
			$data['payment_tinkoff_credit_sort_order'] = $this->config->get('payment_tinkoff_credit_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/tinkoff_credit', $data));
	}

	protected function validate() {

		if (!$this->user->hasPermission('modify', 'extension/payment/tinkoff_credit')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}