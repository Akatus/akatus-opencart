<?php 

class ControllerPaymentakatust extends Controller 
{
	private $error = array(); 
	
	public function index() 
	{
	
		$this->load->language('payment/akatust');
		
		$titulo = 'Transferência Eletrônica - Akatus';
		$this->load->model('setting/setting');
		$this->document->setTitle($titulo);
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') 
		{
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('akatust', $this->request->post);				
			$this->session->data['success'] = 'Dados foram salvos com sucesso!';
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}
	
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
				
		if (isset($this->error['encryption'])) {
		$this->data['error_encryption'] = $this->error['encryption'];
		} else {
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
		 'href'      => HTTPS_SERVER . 'index.php?route=payment/akatust',
		 'text'      => 'Transferência akatust',
		 'separator' => ' :: '
		 );
		
						
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/akatust&token=' . $this->session->data['token'];
				
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		$this->load->model('localisation/order_status');

        $current_settings = $this->model_setting_setting->getSetting('akatust');

		if (isset($this->request->post['akatust_sort_order'])) 
		{
		$this->data['akatust_sort_order'] = $this->request->post['akatust_sort_order'];
		} else {
		$this->data['akatust_sort_order'] = isset($current_settings['akatust_sort_order']) ? $current_settings['akatust_sort_order'] : ''; 
		} 
		
		if (isset($this->request->post['akatust_status'])) {
		$this->data['akatust_status'] = $this->request->post['akatust_status'];
		} else {
		$this->data['akatust_status'] = isset($current_settings['akatust_status']) ? $current_settings['akatust_status'] : ''; 
		} 

		if (isset($this->request->post['akatus_tipo_conta'])) 
		{
			$this->data['akatus_tipo_conta'] = $this->request->post['akatus_tipo_conta'];
		} 
		else 
		{
			$this->data['akatus_tipo_conta'] = isset($current_settings['akatus_tipo_conta']) ? $current_settings['akatus_tipo_conta'] : ''; 
		} 
	
		if (isset($this->request->post['akatust_nome'])) {
		$this->data['akatust_nome'] = $this->request->post['akatust_nome'];
		} else {
		$this->data['akatust_nome'] = isset($current_settings['akatust_nome']) ? $current_settings['akatust_nome'] : ''; 
		} 
		
		
		if (isset($this->request->post['akatust_inicio'])) {
		$this->data['akatust_inicio'] = $this->request->post['akatust_inicio'];
		} else {
		$this->data['akatust_inicio'] = isset($current_settings['akatust_inicio']) ? $current_settings['akatust_inicio'] : ''; 
		} 
		
		
		if (isset($this->request->post['akatust_desconto'])) {
		$this->data['akatust_desconto'] = $this->request->post['akatust_desconto'];
		} else {
		$this->data['akatust_desconto'] = isset($current_settings['akatust_desconto']) ? $current_settings['akatust_desconto'] : ''; 
		}  
		
		if (isset($this->request->post['akatust_padrao'])) {
		$this->data['akatust_padrao'] = $this->request->post['akatust_padrao'];
		} else {
		$this->data['akatust_padrao'] = isset($current_settings['akatust_padrao']) ? $current_settings['akatust_padrao'] : ''; 
		} 
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['akatust_geo_zone_id'])) {
		$this->data['akatust_geo_zone_id'] = $this->request->post['akatust_geo_zone_id'];
		} else {
		$this->data['akatust_geo_zone_id'] = isset($current_settings['akatust_geo_zone_id']) ? $current_settings['akatust_geo_zone_id'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_email_conta'])) {
		$this->data['akatus_email_conta'] = $this->request->post['akatus_email_conta'];
		} else {
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

		if (isset($this->request->post['akatus_token_nip'])) {
		$this->data['akatus_token_nip'] = $this->request->post['akatus_token_nip'];
		} else {
		$this->data['akatus_token_nip'] = isset($current_settings['akatus_token_nip']) ? $current_settings['akatus_token_nip'] : ''; 
		} 
		
		if (isset($this->request->post['akatus_api_key'])) {
		$this->data['akatus_api_key'] = $this->request->post['akatus_api_key'];
		} else {
		$this->data['akatus_api_key'] = isset($current_settings['akatus_api_key']) ? $current_settings['akatus_api_key'] : ''; 
		} 
		
		
				
		$this->load->model('localisation/geo_zone');
												
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
				
		$this->template = 'payment/akatust.tpl';
		$this->children = array(
		'common/header',	
		'common/footer'	
		);
				
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

}
?>
