<?php 

class ControllerPaymentAkatusb extends Controller 
{
	private $error = array(); 
	
	public function index() 
	{
		$this->load->language('payment/akatusb');
		
		$titulo = 'Boleto Akatus';
		$this->load->model('setting/setting');
		$this->document->setTitle($titulo);
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') 
		{
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('akatusb', $this->request->post);				
			$this->session->data['success'] = 'Dados foram salvos com sucesso!';
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		if (isset($this->error['warning'])) 
		{
			$this->data['error_warning'] = $this->error['warning'];
		} 
		else 
		{
			$this->data['error_warning'] = '';
		}
	
		if (isset($this->error['email'])) 
		{
			$this->data['error_email'] = $this->error['email'];
		} 
		else 
		{
			$this->data['error_email'] = '';
		}
			
		if (isset($this->error['encryption'])) 
		{
			$this->data['error_encryption'] = $this->error['encryption'];
		}
		else 
		{
			$this->data['error_encryption'] = '';
		}

		$this->document->breadcrumbs = array();
		
		$this->document->breadcrumbs[] = array(
		 'href'      => HTTPS_SERVER . 'index.php?route=common/home',
		 'text'      => 'Inicial',
		 'separator' => FALSE
		 );
		
		$this->document->breadcrumbs[] = array(
		 'href'      => HTTPS_SERVER . 'index.php?route=extension/payment',
		 'text'      => 'Pagamentos',
		 'separator' => ' :: '
		 );
		
		$this->document->breadcrumbs[] = array(
		 'href'      => HTTPS_SERVER . 'index.php?route=payment/akatusb',
		 'text'      => 'Boleto akatusb',
		 'separator' => ' :: '
		 );
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/akatusb&token=' . $this->session->data['token'];
				
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		$this->load->model('localisation/order_status');
		
        $current_settings = $this->model_setting_setting->getSetting('akatusb');
			
		if (isset($this->request->post['akatusb_sort_order'])) 
		{
			$this->data['akatusb_sort_order'] = $this->request->post['akatusb_sort_order'];
		} 
		else 
		{
			$this->data['akatusb_sort_order'] = isset($current_settings['akatusb_sort_order']) ? $current_settings['akatusb_sort_order'] : ''; 
		} 

		if (isset($this->request->post['akatusb_status'])) 
		{
			$this->data['akatusb_status'] = $this->request->post['akatusb_status'];
		} 
		else 
		{
			$this->data['akatusb_status'] = isset($current_settings['akatusb_status']) ? $current_settings['akatusb_status'] : ''; 
		} 

		if (isset($this->request->post['akatus_tipo_conta'])) 
		{
			$this->data['akatus_tipo_conta'] = $this->request->post['akatus_tipo_conta'];
		} 
		else 
		{
			$this->data['akatus_tipo_conta'] = isset($current_settings['akatus_tipo_conta']) ? $current_settings['akatus_tipo_conta'] : ''; 
		} 

		if (isset($this->request->post['akatusb_nome'])) 
		{
			$this->data['akatusb_nome'] = $this->request->post['akatusb_nome'];
		} 
		else 
		{
			$this->data['akatusb_nome'] = isset($current_settings['akatusb_nome']) ? $current_settings['akatusb_nome'] : ''; 
		} 


		if (isset($this->request->post['akatusb_inicio'])) 
		{
			$this->data['akatusb_inicio'] = $this->request->post['akatusb_inicio'];
		} 
		else 
		{
			$this->data['akatusb_inicio'] = isset($current_settings['akatusb_inicio']) ? $current_settings['akatusb_inicio'] : ''; 
		} 
		
		
		if (isset($this->request->post['akatusb_desconto'])) 
		{
			$this->data['akatusb_desconto'] = number_format(str_replace(',', '.', $this->request->post['akatusb_desconto']), 2, '.', '');
			
		} 
		else 
		{
			$this->data['akatusb_desconto'] = isset($current_settings['akatusb_desconto']) ? $current_settings['akatusb_desconto'] : ''; 
			
		}  

		if (isset($this->request->post['akatusb_padrao'])) 
		{
			$this->data['akatusb_padrao'] = $this->request->post['akatusb_padrao'];
		} 
		else 
		{
			$this->data['akatusb_padrao'] = isset($current_settings['akatusb_padrao']) ? $current_settings['akatusb_padrao'] : ''; 
		} 

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['akatusb_geo_zone_id'])) 
		{
			$this->data['akatusb_geo_zone_id'] = $this->request->post['akatusb_geo_zone_id'];
		} 
		else 
		{
			$this->data['akatusb_geo_zone_id'] = isset($current_settings['akatusb_geo_zone_id']) ? $current_settings['akatusb_geo_zone_id'] : ''; 
		} 

		if (isset($this->request->post['akatus_email_conta'])) 
		{
			$this->data['akatus_email_conta'] = $this->request->post['akatus_email_conta'];
		} 
		else 
		{
			$this->data['akatus_email_conta'] = isset($current_settings['akatus_email_conta']) ? $current_settings['akatus_email_conta'] : ''; 
		} 

		if (isset($this->request->post['akatus_public_token'])) 
		{
			$this->data['akatus_public_token'] = $this->request->post['akatus_public_token'];
		} 
		else 
		{
			$this->data['akatus_public_token'] = isset($current_settings['akatus_public_token']) ? $current_settings['akatus_public_token'] : ''; 
		} 

		if (isset($this->request->post['akatus_token_nip'])) 
		{
			$this->data['akatus_token_nip'] = $this->request->post['akatus_token_nip'];
		} 
		else 
		{
			$this->data['akatus_token_nip'] = isset($current_settings['akatus_token_nip']) ? $current_settings['akatus_token_nip'] : ''; 
		} 

		if (isset($this->request->post['akatusb_api_key'])) 
		{
			$this->data['akatus_api_key'] = $this->request->post['akatus_api_key'];
		} 
		else 
		{
			$this->data['akatus_api_key'] = isset($current_settings['akatus_api_key']) ? $current_settings['akatus_api_key'] : ''; 
		} 

		$this->load->model('localisation/geo_zone');
												
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
				
		$this->template = 'payment/akatusb.tpl';
		$this->children = array(
		'common/header',	
		'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
}
?>
