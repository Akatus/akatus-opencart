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

class ModelPaymentakatusb extends Model 
{
  	public function getMethod($address) {
  		if ($this->config->get('akatusb_status')) {
        		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('akatusb_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
  			
  			if (!$this->config->get('akatusb_geo_zone_id')) {
          		$status = TRUE;
        		} elseif ($query->num_rows) {
        		  	$status = TRUE;
        		} else {
       	  		$status = FALSE;
  			}	
        	} else {
  			$status = FALSE;
  		}
  		
  		$method_data = array();
  	
  		if ($status) {  
        		$method_data = array( 
          		'code'         => 'akatusb',
          		'title'      => $this->config->get('akatusb_nome'),
  				'sort_order' => $this->config->get('akatusb_sort_order')
        		);
      	}
     
      	return $method_data;
  	}

}
?>