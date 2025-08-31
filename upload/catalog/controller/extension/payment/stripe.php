<?php
class ControllerExtensionPaymentStripe extends Controller
{
	public function getMethod($address, $total)
	{
		$this->load->language('extension/payment/stripe');

		$method_data = [];

		if ($this->config->get('payment_stripe_status')) {
			$method_data = [
				'code' => 'stripe',
				'name' => $this->language->get('text_title'),
				'option' => [
					'stripe' => [
						'code' => 'stripe.stripe',
						'name' => $this->language->get('text_title')
					]
				],
				'sort_order' => $this->config->get('payment_stripe_sort_order'),
				'terms' => '',
				'title' => $this->language->get('text_title')
			];
		}

		return $method_data;
	}

	public function index()
	{
		$this->load->language('extension/payment/stripe');
		$this->load->model('extension/payment/stripe');

		if ($this->config->get('payment_stripe_environment') == 'live') {
			$data['publishable_key'] = $this->config->get('payment_stripe_live_publishable_key');
		} else {
			$data['publishable_key'] = $this->config->get('payment_stripe_test_publishable_key');
		}

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_start_date'] = $this->language->get('text_start_date');
		$data['text_wait'] = $this->language->get('text_wait');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_cc_type'] = $this->language->get('entry_cc_type');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_issue'] = $this->language->get('entry_cc_issue');

		$data['help_start_date'] = $this->language->get('help_start_date');
		$data['help_issue'] = $this->language->get('help_issue');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['can_store_cards'] = ($this->customer->isLogged() && $this->config->get('payment_stripe_store_cards'));
		$data['cards'] = [];

		if ($this->customer->isLogged() && $this->config->get('payment_stripe_store_cards')) {
			$data['cards'] = $this->model_extension_payment_stripe->getCards($this->customer->getId());
		}

		return $this->load->view('extension/payment/stripe', $data);
	}

	public function send()
	{
		$json = array();

		$this->load->library('stripe');
		$this->load->model('checkout/order');
		$this->load->model('account/customer');
		$this->load->model('extension/payment/stripe');

		$stripe_environment = $this->config->get('payment_stripe_environment');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($this->initStripe()) {
			try {
				$use_existing_card = json_decode($this->request->post['existingCard']);
				$stripe_customer_id = '';

				// Create Payment Intent parameters
				$payment_intent_params = [
					'amount' => round($order_info['total'] * 100),
					'currency' => strtolower($this->config->get('payment_stripe_currency')),
					'metadata' => [
						'order_id' => $this->session->data['order_id'],
						'opencart_customer_id' => $this->customer->isLogged() ? $this->customer->getId() : 'guest'
					],
					'automatic_payment_methods' => ['enabled' => false] // We'll handle payment methods manually
				];

				// Handle customer creation/management
				if ($this->customer->isLogged()) {
					$stripe_customer = $this->model_extension_payment_stripe->getCustomer($this->customer->getId());

					if (!$stripe_customer) {
						$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

						$stripe_customer_data = \Stripe\Customer::create([
							'email' => $customer_info['email'],
							'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname'],
							'metadata' => [
								'opencart_customer_id' => $this->customer->getId()
							]
						]);

						$this->model_extension_payment_stripe->addCustomer(
							$stripe_customer_data,
							$this->customer->getId(),
							$stripe_environment
						);

						$stripe_customer = $this->model_extension_payment_stripe->getCustomer($this->customer->getId());
					}

					$payment_intent_params['customer'] = $stripe_customer['stripe_customer_id'];
				}

				// Handle payment method
				if ($use_existing_card && $stripe_customer) {
					// Use existing payment method
					$payment_intent_params['payment_method'] = $this->request->post['card'];
				} else {
					// Create new payment method from token
					$payment_method = \Stripe\PaymentMethod::create([
						'type' => 'card',
						'card' => ['token' => $this->request->post['card']]
					]);

					$payment_intent_params['payment_method'] = $payment_method->id;

					// Save card if requested
					if ($this->customer->isLogged() && json_decode($this->request->post['saveCreditCard'])) {
						$this->model_extension_payment_stripe->addPaymentMethod(
							$payment_method,
							$this->customer->getId(),
							$stripe_environment
						);
					}
				}

				// Create Payment Intent
				$payment_intent = \Stripe\PaymentIntent::create($payment_intent_params);

				// Confirm the Payment Intent
				$confirmed_payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent->id);
				$confirmed_payment_intent->confirm();

				if ($confirmed_payment_intent->status === 'succeeded') {
					$this->model_extension_payment_stripe->addOrder($order_info, $confirmed_payment_intent->id, $stripe_environment);
					$message = 'Payment Intent ID: ' . $confirmed_payment_intent->id . ' Status: ' . $confirmed_payment_intent->status;
					$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_stripe_order_status_id'), $message, false);
					$json['processed'] = true;
					$json['success'] = $this->url->link('checkout/success');
				} else {
					$json['error'] = 'Payment failed: ' . $confirmed_payment_intent->status;
				}

			} catch (\Stripe\Exception\ApiErrorException $e) {
				$json['error'] = 'Payment error: ' . $e->getMessage();
			} catch (Exception $e) {
				$json['error'] = 'An error occurred: ' . $e->getMessage();
			}
		} else {
			$json['error'] = 'Stripe configuration error. Please contact administrator.';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function initStripe()
	{
		$this->load->library('stripe');
		if ($this->config->get('payment_stripe_environment') == 'live') {
			$stripe_secret_key = $this->config->get('payment_stripe_live_secret_key');
		} else {
			$stripe_secret_key = $this->config->get('payment_stripe_test_secret_key');
		}

		if ($stripe_secret_key != '' && $stripe_secret_key != null) {
			\Stripe\Stripe::setApiKey($stripe_secret_key);
			return true;
		}

		return false;

	}
}
