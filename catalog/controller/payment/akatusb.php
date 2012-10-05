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

class ControllerPaymentAkatusb extends Controller 
{
  protected function index() 
  {
    $this->data['button_confirm'] = 'Confirmar';
	$this->data['button_back'] = 'Voltar';

	
	$this->load->model('checkout/order');
	$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

    $this->data['pedido'] = $order_info['order_id'];
	
	$this->data['desconto'] = $this->config->get('akatusb_desconto');
	$this->data['valorpedido'] = $order_info['total'];
	
    $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
    $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/success';
    if ($this->request->get['route'] != 'checkout/guest_step_3') {
 	 $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
    } else {
      $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
    }	
    $this->id = 'payment';
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/akatusb.tpl')) {
	  $this->template = $this->config->get('config_template') . '/template/payment/akatusb.tpl';
	} else {
	  $this->template = 'default/template/payment/akatusb.tpl';
	}	
	$this->render();
  }

  public function confirm() {

		$this->load->model('checkout/order');
		
		#Envia os dados para a Akatus na tentativa de receber
		
		// Registry
		$registry = new Registry();
		
		// Loader
		$loader = new Loader($registry);
		$registry->set('load', $loader);
		
		// Config
		$config = new Config();
		$registry->set('config', $config);

		
		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		
		$registry->set('db', $db);
		$pedido = $db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '".$this->session->data['order_id']."'");
		
	
		$valor_total_compra=number_format($pedido->row['total'], 2, '.', '') ;
		
		$desconto=$this->config->get('akatusb_desconto');
		
		if($desconto)
		{
			$valor_total_compra=number_format($valor_total_compra-($valor_total_compra*($desconto/100)), 2, '.', '');
		}
		
				
		  $xml=utf8_encode('<?xml version="1.0" encoding="utf-8"?><carrinho>
			<recebedor>
				<api_key>'. $this->config->get('akatus_api_key').'</api_key>
				<email>'.$this->config->get('akatus_email_conta').'</email>
			</recebedor>
			<pagador>
				<nome>'.$pedido->row['firstname'].' '.$pedido->row['lastname'].'</nome>
				<email>'.$pedido->row['email'].'</email>
				
				<telefones>
					<telefone>
						<tipo>residencial</tipo>
						<numero>'.substr(preg_replace("/[^0-9]/","",$pedido->row['telephone']), 0, 11).'</numero>

					</telefone>
				</telefones>
			</pagador>
			<produtos>
			   
				<produto>
					<codigo>1</codigo>
					<descricao>Pedido '.$pedido->row['order_id'].' em http://'.$_SERVER['HTTP_HOST'].'/</descricao>
					<quantidade>1</quantidade>
					<preco>'.$valor_total_compra.'</preco>
					<peso>0.0</peso>
					<frete>0.00</frete>
					<desconto>0.00</desconto>
				</produto>
			</produtos>
			
			<transacao>
			
			<desconto_total>'.(    number_format(($this->config->get('akatusb_desconto')/100) * $valor_total_compra, 2, '.', '')  ).'</desconto_total>
			<peso_total>0.00</peso_total>
			<frete_total>0.00</frete_total>
			<moeda>BRL</moeda>
			<referencia>'.($pedido->row['order_id']).'</referencia>
			<meio_de_pagamento>boleto</meio_de_pagamento>
			
			</transacao>
		
		</carrinho>');
		
		
			$URL = "https://www.akatus.com/api/v1/carrinho.xml";

			$ch = curl_init($URL);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt($ch, CURLOPT_POST, 1);

			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml");

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$akatus = curl_exec($ch);

			curl_close($ch);


			$akatus=$this->xml2array($akatus);
		
		
		$comment  = "Link para o pagamento do Boleto Bancário: \n<br>";
		$comment .= '<a href="'.$akatus['resposta']['url_retorno'].'" target="_blank">'.$akatus['resposta']['url_retorno'].'</a>';
		
		
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('akatusb_padrao'), $comment);
		
		if (!empty($this->session->data['order_id'])) 
		{ 
			$this->cart->clear();
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
		}
		
        $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=5&url_boleto=".urlencode($akatus['resposta']['url_retorno'])."';</script>";  

		
		$this->response->setOutput($ouput);
	}
	
	public function xml2array($contents, $get_attributes=1, $priority = 'tag') 
	{ 
    if(!$contents) return array(); 

    if(!function_exists('xml_parser_create')) { 
        //print "'xml_parser_create()' function not found!"; 
        return array(); 
    } 

    //Get the XML parser of PHP - PHP must have this module for the parser to work 
    $parser = xml_parser_create(''); 
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, trim($contents), $xml_values); 
    xml_parser_free($parser); 

    if(!$xml_values) return;//Hmm... 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array; //Refference 

    //Go through the tags. 
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
    foreach($xml_values as $data) { 
        unset($attributes,$value);//Remove existing values, or there will be trouble 

        //This command will extract these variables into the foreach scope 
        // tag(string), type(string), level(int), attributes(array). 
        extract($data);//We could use the array by itself, but this cooler. 

        $result = array(); 
        $attributes_data = array(); 
         
        if(isset($value)) { 
            if($priority == 'tag') $result = $value; 
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
        } 

        //Set the attributes too. 
        if(isset($attributes) and $get_attributes) { 
            foreach($attributes as $attr => $val) { 
                if($priority == 'tag') $attributes_data[$attr] = $val; 
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
            } 
        } 

        //See tag status and do the needed. 
        if($type == "open") {//The starting of the tag '<tag>' 
            $parent[$level-1] = &$current; 
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                $current[$tag] = $result; 
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 

                $current = &$current[$tag]; 

            } else { //There was another element with the same tag name 

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                    $repeated_tag_index[$tag.'_'.$level]++; 
                } else {//This section will make the value an array if multiple tags with the same name appear together 
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array 
                    $repeated_tag_index[$tag.'_'.$level] = 2; 
                     
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                        unset($current[$tag.'_attr']); 
                    } 

                } 
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                $current = &$current[$tag][$last_item_index]; 
            } 

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
            //See if the key is already taken. 
            if(!isset($current[$tag])) { //New Key 
                $current[$tag] = $result; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

            } else { //If taken, put all things inside a list(array) 
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

                    // ...push the new element into that array. 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                     
                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; 

                } else { //If it is not an array... 
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $get_attributes) { 
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                             
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
                         
                        if($attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                } 
            } 

        } elseif($type == 'close') { //End of tag '</tag>' 
            $current = &$parent[$level-1]; 
        } 
    } 
     
    return($xml_array); 
	} 
}
?>

