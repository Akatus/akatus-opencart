<?php  
class ControllerCheckoutCheckout extends Controller { 

    public function index() {
        $this->language->load('checkout/checkout');

        $this->load->model('account/address');        
        $this->load->model('setting/extension');
		$this->load->model('localisation/country');
        $this->load->model('account/customer_group');
        
		$this->document->setTitle($this->language->get('heading_title')); 
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
					
		$this->data['breadcrumbs'] = array();
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	); 
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_cart'),
			'href'      => $this->url->link('checkout/cart'),
        	'separator' => $this->language->get('text_separator')
      	);
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
					
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/checkout.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/checkout.tpl';
		} else {
			$this->template = 'default/template/checkout/checkout.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);        
        
        $this->validateStock();
        $this->loadData();
        
        if ($this->customer->isLogged()) {
            $this->loadDataFromLoggedCustomer();
        }
        
        $this->findShippingMethods($ajax = false);
        $this->loadPaymentMethods();
        $this->calculate($ajax = false);
        $this->parcelamento($ajax = false);
        
		$this->response->setOutput($this->render());
  	}
	
    public function validate() {
        if ($_POST) {
            
            $this->load->model('account/address');
            $this->load->model('account/customer');
            $this->load->model('localisation/country');
            
            $this->language->load('checkout/checkout');
            
            $this->validateStock();
            $this->saveAddress();
            
            if (! $this->customer->isLogged()) {
                
                $this->validateUserRegistration();
                
                if ($this->data['error']) {
                    $this->data = array_merge($this->data, $this->request->post);
                    return $this->index();
                    
                } else {
                    $this->saveCustomerPlusAddress();
                    $this->loginJustRegisteredUser();                    
                }
            }
            
            return $this->forward($this->createRedirectUrl());
        }       
    }
    
    private function validateStock()
    {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->url->link('checkout/cart'));
		}

		// Validate minimum quantity requirments.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->redirect($this->url->link('checkout/cart'));

				break;
			}
		}
    }    
    
    private function saveAddress()
    {
        $paymentAddress = array();
        $paymentAddress['firstname'] = isset($this->request->post['firstname']) ? $this->request->post['firstname'] : '';
        $paymentAddress['lastname'] = isset($this->request->post['lastname']) ? $this->request->post['lastname'] : '';
        $paymentAddress['address_1'] = isset($this->request->post['address_1']) ? $this->request->post['address_1'] : '';
        $paymentAddress['address_2'] = isset($this->request->post['address_2']) ? $this->request->post['address_2'] : '';
        $paymentAddress['postcode'] = isset($this->request->post['postcode']) ? $this->request->post['postcode'] : '';
        $paymentAddress['city'] = isset($this->request->post['city']) ? $this->request->post['city'] : '';
        $paymentAddress['zone_id'] = isset($this->request->post['zone_id']) ? $this->request->post['zone_id'] : '';
        $paymentAddress['country_id'] = isset($this->request->post['country_id']) ? $this->request->post['country_id'] : '';              
        
        $shippingAddress = array();
        $shippingAddress['firstname'] = isset($this->request->post['shipping_firstname']) ? $this->request->post['shipping_firstname'] : '';
        $shippingAddress['lastname'] = isset($this->request->post['shipping_lastname']) ? $this->request->post['shipping_lastname'] : '';
        $shippingAddress['address_1'] = isset($this->request->post['shipping_address_1']) ? $this->request->post['shipping_address_1'] : '';
        $shippingAddress['address_2'] = isset($this->request->post['shipping_address_2']) ? $this->request->post['shipping_address_2'] : '';
        $shippingAddress['postcode'] = isset($this->request->post['shipping_postcode']) ? $this->request->post['shipping_postcode'] : '';
        $shippingAddress['city'] = isset($this->request->post['shipping_city']) ? $this->request->post['shipping_city'] : '';
        $shippingAddress['zone_id'] = isset($this->request->post['shipping_zone_id']) ? $this->request->post['shipping_zone_id'] : '';
        $shippingAddress['country_id'] = isset($this->request->post['shipping_country_id']) ? $this->request->post['shipping_country_id'] : ''; 
        
        $logged = $this->customer->isLogged();
        $newShippingAddress = $this->request->post['shipping_address'] === 'new';
        
        if ($logged) {
            $newPaymentAddress = $this->request->post['payment_address'] === 'new';
            
            if ($newPaymentAddress) {
                $this->model_account_address->addAddress($paymentAddress);
            } else {
                $address_id = $this->request->post['address_id'];
                $paymentAddress = $this->model_account_address->getAddress($address_id);                
            }
            
            if ($newShippingAddress) {
                $this->model_account_address->addAddress($newShippingAddress);
            } else {
                $shipping_address_id = $this->request->post['shipping_address_id'];
                $shippingAddress = $this->model_account_address->getAddress($shipping_address_id);                                
            }
            
        } else {
            if ($newShippingAddress) {
                $this->model_account_address->addAddress($shippingAddress);
            } else {
                $shippingAddress = $paymentAddress;
            }
        }
        
        $this->session->data['payment_address'] = $paymentAddress;
        $this->session->data['shipping_address'] = $shippingAddress;
    }    
    
    private function validateUserRegistration() {
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->data['error']['firstname'] = 'O nome deve ter entre 1 e 32 caracteres.';
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->data['error']['lastname'] = 'O sobrenome deve ter entre 1 e 32 caracteres.';
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->data['error']['email'] = 'O e-mail informado não é válido.';
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->data['error_warning'] = 'Atenção: Este e-mail já está cadastrado em nossa loja.';
        }

        if ((utf8_strlen($this->request->post['telephone']) < 10) || (utf8_strlen($this->request->post['telephone']) > 11)) {
            $this->data['error']['telephone'] = 'O telefone deve ter entre 10 e 11 dígitos, incluindo o DDD.';
        }

        if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
            $this->data['error']['address_1'] = 'O endereço deve ter entre 3 e 128 caracteres.';
        }

        if ((utf8_strlen($this->request->post['address_2']) < 3) || (utf8_strlen($this->request->post['address_2']) > 30)) {
            $this->data['error']['address_2'] = 'O bairro deve ter entre 3 e 30 caracteres.';
        }        
        
        if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 50)) {
            $this->data['error']['city'] = 'A cidade deve ter entre 2 e 50 caracteres.';
        }

        if (utf8_strlen($this->request->post['postcode'] != 8)) {
            $this->data['error']['postcode'] = 'O CEP deve ter 8 digítos.';
        }

        if ($this->request->post['country_id'] == '') {
            $this->data['error']['country'] = 'Selecione um país.';
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $this->data['error']['zone'] = 'Selecione um estado.';
        }

        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->data['error']['password'] = 'A senha deve ter entre 4 e 20 caracteres.';
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->data['error']['confirm'] = 'A confirmação da senha deve ser igual a senha.';
        }
        
        if ($this->request->post['shipping_address'] != 'same') {
            if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
                $this->data['error']['shipping_firstname'] = 'O nome deve ter entre 1 e 32 caracteres.';
            }

            if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
                $this->data['error']['shipping_lastname'] = 'O sobrenome deve ter entre 1 e 32 caracteres.';
            }

            if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
                $this->data['error']['shipping_address_1'] = 'O endereço deve ter entre 3 e 128 caracteres.';
            }

            if ((utf8_strlen($this->request->post['shipping_address_2']) < 3) || (utf8_strlen($this->request->post['shipping_address_2']) > 30)) {
                $this->data['error']['shipping_address_2'] = 'O bairro deve ter entre 3 e 30 caracteres.';
            }        

            if ((utf8_strlen($this->request->post['shipping_city']) < 2) || (utf8_strlen($this->request->post['shipping_city']) > 50)) {
                $this->data['error']['shipping_city'] = 'A cidade deve ter entre 2 e 50 caracteres.';
            }

            if (utf8_strlen($this->request->post['shipping_postcode'] != 8)) {
                $this->data['error']['shipping_postcode'] = 'O CEP deve ter 8 digítos.';
            }

            if ($this->request->post['shipping_country_id'] == '') {
                $this->data['error']['shipping_country'] = 'Selecione um país.';
            }

            if (!isset($this->request->post['shipping_zone_id']) || $this->request->post['shipping_zone_id'] == '') {
                $this->data['error']['zone'] = 'Selecione um estado.';
            }            
        }
    }
    
    private function saveCustomerPlusAddress()
    {
        $this->model_account_customer->addCustomer($this->request->post);

        $this->session->data['account'] = 'register';

        $payment_method = $this->request->post['payment_method'];
        $this->load->model('payment/' . $payment_method);
        $this->session->data['payment_method'] = $this->{'model_payment_' . $payment_method}->getMethod();
    }

    private function loginJustRegisteredUser()
    {
        $this->customer->login($this->request->post['email'], $this->request->post['password']);
    }    

    private function createRedirectUrl()
    {
        $paymentMethod = $this->request->post['payment_method'];
        
        $url = 'payment/' . $paymentMethod;
        
        if ($paymentMethod === 'akatust') {
            $url .= '&tef=' . $this->request->post['tef'];
        }
        
        return $url;
    }    
    
    public function findShippingMethods($ajax = true) {
        $countryId = null;
        $zoneId = null;
        
        if ($ajax) {
            $countryId = $this->request->get['country_id'];
            $zoneId = $this->request->get['zone_id'];
            
        } else if ($this->customer->isLogged()) {
            // TODO: modificar de acordo com o change
            $countryId = $this->session->data['shipping_address']['country_id'];
            $zoneId = $this->session->data['shipping_address']['zone_id'];
                
        } else {
            $countryId = 30; // Brazil
            $zoneId = 464; // Sao Paulo                
        }
        
        $parametros = array(
            'country_id'    => $countryId,
            'zone_id'       => $zoneId
        );
        
        $quote_data = array();

        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('shipping');

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);

                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($parametros); 

                if ($quote) {
                    $quote_data[$result['code']] = array( 
                        'title'      => $quote['title'],
                        'quote'      => $quote['quote'], 
                        'sort_order' => $quote['sort_order'],
                        'error'      => $quote['error']
                    );
                }
            }
        }

        $sort_order = array();

        foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $quote_data);

        $this->session->data['shipping_methods'] = $quote_data;
        $this->data['shipping_methods'] = $quote_data;
        
        if ($ajax) {
            return $this->response->setOutput(json_encode($quote_data));
        }
    }
    
	public function country() {
		$json = array();

		$this->load->model('localisation/country');

    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->setOutput(json_encode($json));
	}    
    
    private function loadData() {
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();
        $this->data['products'] = $this->cart->getProducts();
		$this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $this->data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount'      => $this->currency->format($voucher['amount'])
                );
            }
        }

	    $this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_modify'] = $this->language->get('text_modify');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_address_new'] = $this->language->get('text_address_new');
		$this->data['text_none'] = $this->language->get('text_none');

		$this->data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		
		if (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];		
		} else {
			$this->data['postcode'] = '';
		}
		
    	if (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];		
		} else {	
      		$this->data['country_id'] = $this->config->get('config_country_id');
    	}
		
    	if (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];			
		} else {
      		$this->data['zone_id'] = '';
    	}
				
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->session->data['payment_country_id'])) {
			$this->data['country_id'] = $this->session->data['payment_country_id'];
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['payment_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['payment_zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$this->data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['code'] = '';
		}
    }    
    
    private function loadDataFromLoggedCustomer() {
        $addresses = $this->model_account_address->getAddresses();
        $address = reset($addresses);

        $this->session->data['addresses'] = $addresses;
        $this->session->data['shipping_address'] = $address;
        $this->session->data['payment_address'] = $address;

        $this->data['addresses'] = $addresses;
        $this->data['address_id'] = $address['address_id'];
        $this->data['shipping_address_id'] = $address['address_id'];

        $quote_data = array();

        $results = $this->model_setting_extension->getExtensions('shipping');

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);

                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address); 

                if ($quote) {
                    $quote_data[$result['code']] = array( 
                        'title'      => $quote['title'],
                        'quote'      => $quote['quote'], 
                        'sort_order' => $quote['sort_order'],
                        'error'      => $quote['error']
                    );
                }
            }
        }

        $sort_order = array();

        foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $quote_data);

        $this->session->data['shipping_methods'] = $quote_data;
        
        // Totals
        $total_data = array();					
        $sort_order = array(); 
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }
    }

    private function loadPaymentMethods() {
        $method_data = array();

        $results = $this->model_setting_extension->getExtensions('payment');

        foreach ($results as $result) {
            $module_name = $result['code'];
            $module_enabled = $this->config->get($module_name . '_status');
            
            // TODO: testar com outros meios de pagamento
            $akatus_modules = array('akatus', 'akatusb', 'akatust');

            if ($module_enabled && in_array($module_name, $akatus_modules)) {
                $this->load->model('payment/' . $module_name);

                $method = $this->{'model_payment_' . $module_name}->getMethod(); 

                if ($method) {
                    $method_data[$module_name] = $method;
                }
            }
        }

        $sort_order = array(); 

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);			

        if (empty($method_data)) {
            $this->session->data['payment_methods'] = array();	
            $this->data['payment_methods'] = array();            
        } else {
            $this->session->data['payment_methods'] = $method_data;	
            $this->data['payment_methods'] = $method_data;            
        }
    }

    public function calculate($ajax = true) {
        if ($ajax) {
            if ($this->request->post['shipping_method']) {
                $shipping = explode('.', $this->request->post['shipping_method']);
                $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
            }  
        }
        
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            $module_name = $result['code'];
            
            if ($this->config->get($module_name . '_status')) {
                $this->load->model('total/' . $module_name);

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);

        $this->language->load('checkout/checkout');

        $product_data = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $value = $this->encryption->decrypt($option['option_value']);
                }

                $option_data[] = array(
                    'product_option_id'       => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id'               => $option['option_id'],
                    'option_value_id'         => $option['option_value_id'],
                    'name'                    => $option['name'],
                    'value'                   => $value,
                    'type'                    => $option['type']
                );
            }

            $product_data[] = array(
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'model'      => $product['model'],
                'option'     => $option_data,
                'download'   => $product['download'],
                'quantity'   => $product['quantity'],
                'subtract'   => $product['subtract'],
                'price'      => $product['price'],
                'total'      => $product['total'],
                'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward'     => $product['reward']
            );
        }

        // Gift Voucher
        $voucher_data = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $voucher_data[] = array(
                    'description'      => $voucher['description'],
                    'code'             => substr(md5(mt_rand()), 0, 10),
                    'to_name'          => $voucher['to_name'],
                    'to_email'         => $voucher['to_email'],
                    'from_name'        => $voucher['from_name'],
                    'from_email'       => $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message'          => $voucher['message'],
                    'amount'           => $voucher['amount']
                );
            }
        }

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_total'] = $this->language->get('column_total');

        $this->data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

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
            }

            $this->data['products'][] = array(
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'model'      => $product['model'],
                'option'     => $option_data,
                'quantity'   => $product['quantity'],
                'subtract'   => $product['subtract'],
                'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
                'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        // Gift Voucher
        $this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $this->data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount'      => $this->currency->format($voucher['amount'])
                );
            }
        }

        $this->data['totals'] = $total_data;
        
        if ($ajax) { // TODO: verificar a necessidade do IF/ELSE
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirm.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/checkout/confirm.tpl';
            } else {
                $this->template = 'default/template/checkout/confirm.tpl';
            }
            
            return $this->response->setOutput($this->render());
        }         
    }
    
    public function parcelamento($ajax = true) {
        $juros = 1.99; // TODO: extrair constante
        $taxa = $juros / 100;
        $limiteDeParcelas = $this->config->get('akatus_limite_parcelas');
        $numeroParcelasSemJuros = $this->config->get('akatus_sem_juros');        
        $total = $this->getTotal();

        if ($total > 5) {
            $splitss = (int) ($total / 5);

            if ($splitss <= $limiteDeParcelas) {
                $total_parcelas = $splitss;
            } else {
                $total_parcelas = $limiteDeParcelas;
            }
            
        } else {
            $total_parcelas = 1;
        }

        $parcelamentoHTML = '';

        for($j=1; $j <= $total_parcelas; $j++) {

            if($numeroParcelasSemJuros >= $j) {
                $valorParcela = $total / $j;
                
                $parcelamentoHTML .= '<option value="'.$j.'">'.$j.'x de R$'.number_format($valorParcela, 2,',', '.').' sem juros</option>';

            } else {
                $valorParcela = ($total  * $taxa) / (1-(1 / pow(1 + $taxa, $j)));
                $valorParcela =  round($valorParcela, 2);                

                $parcelamentoHTML .= '<option value="'.$j.'">'.$j.'x de R$'.number_format($valorParcela, 2,',', '.').' com juros de '.number_format($juros, 2, ',', ',').'% a.m.</option>';
            }

        }

        if ($ajax) {
            $this->response->setOutput($parcelamentoHTML);
        } else {
            $this->data['parcelamento'] = $parcelamentoHTML;
        }
    }
    
    private function getTotal() {
        // Totals
        $total_data = array();					
        $total = 0;
        $taxes = $this->cart->getTaxes();
        
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }
        
        return $total;
    }
}