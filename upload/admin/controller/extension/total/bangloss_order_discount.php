<?php
/*
This file is part of "Order Total Discount Rules" project and subject to the terms
and conditions defined in file "LICENSE.txt".
Author: BANGLOSS
Web: https://www.bangloss.com
email: solimankhulna@gmail.com
*/
class ControllerExtensionTotalBanglossOrderDiscount extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/bangloss_order_discount');

		$this->document->setTitle($this->language->get('heading_title_text'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('total_bangloss_order_discount', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total/bangloss_order_discount', 'user_token=' . $this->session->data['user_token'], true));
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/bangloss_order_discount', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/total/bangloss_order_discount', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		        
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();


		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}


		if (isset($this->request->post['total_bangloss_order_discount'])) {
			$data['total_bangloss_order_discount'] = $this->request->post['total_bangloss_order_discount'];
		} else {
			$data['total_bangloss_order_discount'] = $this->config->get('total_bangloss_order_discount');
		}

		if (isset($this->request->post['total_bangloss_order_discount_status'])) {
			$data['total_bangloss_order_discount_status'] = $this->request->post['total_bangloss_order_discount_status'];
		} else {
			$data['total_bangloss_order_discount_status'] = $this->config->get('total_bangloss_order_discount_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/bangloss_order_discount', $data));
	}

	protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/total/bangloss_order_discount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if (isset($this->request->post['total_bangloss_order_discount']['title']) && is_array($this->request->post['total_bangloss_order_discount']['title'])) {
			foreach ($this->request->post['total_bangloss_order_discount']['title'] as $language_id => $discount_title) {
				if (utf8_strlen($discount_title) < 1 || utf8_strlen($discount_title) > 128) {
					$this->error['discount_title'] = $this->language->get('error_discount_title');
				}
			}
		} 


		return !$this->error;
	}
}
