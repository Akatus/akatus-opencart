<?php 
/*
+---------------------------------------------------+
| 			 MÓDULO DE PAGAMENTO AKATUS 			|
|---------------------------------------------------|
|													|
|  Este módulo permite receber pagamentos através   |
|  do gateway de pagamentos Akatus em lojas			|
|  utilizando a plataforma Prestashop				|
|													|
|---------------------------------------------------|
|													|
|  Desenvolvido por: www.andresa.com.br				|
|					 contato@andresa.com.br			|
|													|
+---------------------------------------------------+
*/

/**
 * @author Andresa Martins da Silva
 * @copyright Andresa Web Studio
 * @site http://www.andresa.com.br
 * @version 1.0 Beta
 **/

class ControllerPaymentAkatus extends Controller 
{
	private $error = array(); 

	public function index() 
	{
		$this->load->language('payment/akatus');
		
		$titulo = 'Akatus - Cartões de Crédito';
		$this->load->model('setting/setting');
		$this->document->setTitle($titulo);
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') 
		{
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('akatus', $this->request->post);				
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
		 'href'      => HTTPS_SERVER . 'index.php?route=payment/akatus',
		 'text'      => 'Cartões AKATUS',
		 'separator' => ' :: '
		 );
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/akatus&token=' . $this->session->data['token'];
				
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		$this->load->model('localisation/order_status');
		
        $current_settings = $this->model_setting_setting->getSetting('akatus');

		if (isset($this->request->post['akatus_sort_order'])) 
		{
			$this->data['akatus_sort_order'] = $this->request->post['akatus_sort_order'];
		} 
		else 
		{
			$this->data['akatus_sort_order'] = isset($current_settings['akatus_sort_order']) ? $current_settings['akatus_sort_order'] : ''; 
		} 

		if (isset($this->request->post['akatus_status'])) 
		{
			$this->data['akatus_status'] = $this->request->post['akatus_status'];
		} 
		else 
		{
			$this->data['akatus_status'] = isset($current_settings['akatus_status']) ? $current_settings['akatus_status'] : ''; 
		} 

		if (isset($this->request->post['akatus_tipo_conta'])) 
		{
			$this->data['akatus_tipo_conta'] = $this->request->post['akatus_tipo_conta'];
		} 
		else 
		{
			$this->data['akatus_tipo_conta'] = isset($current_settings['akatus_tipo_conta']) ? $current_settings['akatus_tipo_conta'] : ''; 
		} 

		if (isset($this->request->post['akatus_nome'])) 
		{
			$this->data['akatus_nome'] = $this->request->post['akatus_nome'];
		} 
		else 
		{
			$this->data['akatus_nome'] = isset($current_settings['akatus_nome']) ? $current_settings['akatus_nome'] : ''; 
		} 


		if (isset($this->request->post['akatus_inicio'])) 
		{
			$this->data['akatus_inicio'] = $this->request->post['akatus_inicio'];
		} 
		else 
		{
			$this->data['akatus_inicio'] = isset($current_settings['akatus_inicio']) ? $current_settings['akatus_inicio'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_padrao'])) 
		{
			$this->data['akatus_padrao'] = $this->request->post['akatus_padrao'];
		} 
		else 
		{
			$this->data['akatus_padrao'] = isset($current_settings['akatus_padrao']) ? $current_settings['akatus_padrao'] : ''; 
		} 

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['akatus_geo_zone_id'])) 
		{
			$this->data['akatus_geo_zone_id'] = $this->request->post['akatus_geo_zone_id'];
		} 
		else 
		{
			$this->data['akatus_geo_zone_id'] = isset($current_settings['akatus_geo_zone_id']) ? $current_settings['akatus_geo_zone_id'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_email_conta'])) 
		{
			$this->data['akatus_email_conta'] = $this->request->post['akatus_email_conta'];
		} 
		else 
		{
			$this->data['akatus_email_conta'] = isset($current_settings['akatus_email_conta']) ? $current_settings['akatus_email_conta'] : ''; 
		} 

		
		if (isset($this->request->post['akatus_token_nip'])) 
		{
			$this->data['akatus_token_nip'] = $this->request->post['akatus_token_nip'];
		} 
		else 
		{
			$this->data['akatus_token_nip'] = isset($current_settings['akatus_token_nip']) ? $current_settings['akatus_token_nip'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_api_key'])) 
		{
			$this->data['akatus_api_key'] = $this->request->post['akatus_api_key'];
		} 
		else 
		{
			$this->data['akatus_api_key'] = isset($current_settings['akatus_api_key']) ? $current_settings['akatus_api_key'] : ''; 
		} 

		if (isset($this->request->post['akatus_limite_parcelas'])) 
		{
			$this->data['akatus_limite_parcelas'] = $this->request->post['akatus_limite_parcelas'];
		} 
		else 
		{
			$this->data['akatus_limite_parcelas'] = isset($current_settings['akatus_limite_parcelas']) ? $current_settings['akatus_limite_parcelas'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_sem_juros'])) 
		{
			$this->data['akatus_sem_juros'] = $this->request->post['akatus_sem_juros'];
		} 
		else 
		{
			$this->data['akatus_sem_juros'] = isset($current_settings['akatus_sem_juros']) ? $current_settings['akatus_sem_juros'] : ''; 
		} 


		if (isset($this->request->post['akatus_mensagem_pagamento_analise'])) 
		{
			$this->data['akatus_mensagem_pagamento_analise'] = $this->request->post['akatus_mensagem_pagamento_analise'];
		} 
		else 
		{
			$this->data['akatus_mensagem_pagamento_analise'] = $this->config->get('akatus_mensagem_pagamento_analise'); 
		} 
		
		$this->load->model('localisation/geo_zone');
												
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
				
		$this->template = 'payment/akatus.tpl';
		$this->children = array(
		'common/header',	
		'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
}
?>
