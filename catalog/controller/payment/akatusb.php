<?php

require_once(DIR_SYSTEM . '../akatus/transacao.php');

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
        global $log;

		$this->load->model('checkout/order');
		
        $order_id = $this->session->data['order_id'];
		
		$pedido = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '".$order_id."'");
		$estado = $this->db->query("SELECT code FROM `" . DB_PREFIX . "zone` WHERE zone_id = ".$pedido->row['payment_zone_id']);
		$pais = $this->db->query("SELECT iso_code_3 FROM `" . DB_PREFIX . "country` WHERE country_id = ".$pedido->row['payment_country_id']);
	    
        $produtos_result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = " . $order_id);
		$frete_result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE code = 'shipping' AND order_id = '".$order_id."'");
		$cupom_result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE code = 'coupon' AND order_id = '".$order_id."'");
		$voucher_result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE code = 'voucher' AND order_id = '".$order_id."'");

        $frete = number_format($frete_result->row['value'], 2, '.', '');

        $temCupomDesconto = (count($cupom_result->rows)) ? true : false;
        $temVoucherDesconto = (count($voucher_result->rows)) ? true : false;

        $cupom = 0;
        $voucher = 0;
        $desconto = 0;

        if ($temCupomDesconto) {
            $cupom = abs($cupom_result->rows[0]['value']);
        }
				
        if ($temVoucherDesconto) {
            $voucher = abs($voucher_result->rows[0]['value']);
        }

        $desconto = number_format($cupom + $voucher, 2, '.', '');

        $xml=utf8_encode('<?xml version="1.0" encoding="utf-8"?><carrinho>
        <recebedor>
            <api_key>'. $this->config->get('akatus_api_key').'</api_key>
            <email>'.$this->config->get('akatus_email_conta').'</email>
        </recebedor>
        <pagador>
            <nome>'.utf8_decode($pedido->row['firstname']).' '.utf8_decode($pedido->row['lastname']).'</nome>
            <email>'.$pedido->row['email'].'</email>
            <enderecos>
                <endereco>
                    <tipo>entrega</tipo>
                    <logradouro>'.utf8_decode($pedido->row['payment_address_1']).'</logradouro>
                    <bairro>'.utf8_decode($pedido->row['payment_address_2']).'</bairro>
                    <cidade>'.utf8_decode($pedido->row['payment_city']).'</cidade>
                    <estado>'.utf8_decode($estado->row['code']).'</estado>
                    <pais>'.utf8_decode($pais->row['iso_code_3']).'</pais>
                    <cep>'.$pedido->row['payment_postcode'].'</cep>
                </endereco>
            </enderecos>

            <telefones>
                <telefone>
                    <tipo>residencial</tipo>
                    <numero>'.substr(preg_replace("/[^0-9]/","",$pedido->row['telephone']), 0, 11).'</numero>
                </telefone>
            </telefones>
        </pagador>
        <produtos>');

        foreach($produtos_result->rows as $produto) {
            $valor_produto = number_format($produto['price'], 2, '.', '');

            $xml .= '<produto>
                <codigo>'. $produto['product_id'] .'</codigo>
                <descricao>'. $produto['name'] .'</descricao>
                <quantidade>'. $produto['quantity'] .'</quantidade>
                <preco>'. $valor_produto .'</preco>
                <peso>0.00</peso>
                <frete>0.00</frete>
                <desconto>0.00</desconto>
            </produto>';
        }
			   
        $fingerprint_akatus = isset($_POST['fingerprint_akatus']) ? $_POST['fingerprint_akatus'] : '';
        $fingerprint_partner_id = isset($_POST['fingerprint_partner_id']) ? $_POST['fingerprint_partner_id'] : '';

        $xml .= '</produtos>
        
        <transacao>
        
        <desconto>'. $desconto .'</desconto>
        <peso>0.00</peso>
        <frete>'. $frete .'</frete>
        <moeda>BRL</moeda>
        <referencia>'.$pedido->row['order_id'].'</referencia>
        <meio_de_pagamento>boleto</meio_de_pagamento>

        <fingerprint_akatus>'.$fingerprint_akatus.'</fingerprint_akatus>				
        <fingerprint_partner_id>'.$fingerprint_partner_id.'</fingerprint_partner_id>				
        
        </transacao>
		
		</carrinho>';

        $URL = $this->get_url();

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

        if ($akatus['resposta']['status'] == 'erro') {
            $log->write('URL da requisição: ' . $URL);
            $log->write('Erro ao tentar realizar transação. Dados enviados:');
            $log->write($xml);

            $log->write('Dados recebidos da Akatus:');
            $log->write(print_r($akatus, true));

            $this->model_checkout_order->confirm($order_id, Transacao::ID_FAILED, $comment = '', $notify = false);

            $output = "<script>window.location = 'index.php?route=information/akatus&tipo=4';</script>";
            
        } else {
            $comment = "Link para o pagamento do Boleto Bancário: \n<br>";
            $comment .= '<a href="' . $akatus['resposta']['url_retorno'] . '" target="_blank">' . $akatus['resposta']['url_retorno'] . '</a>';

            $this->model_checkout_order->confirm($order_id, $this->config->get('akatusb_padrao'), $comment, $notify = true);

            $output = "<script>window.location = 'index.php?route=information/akatus&tipo=5&url_boleto=" . urlencode($akatus['resposta']['url_retorno']) . "';</script>";

        }

        if (!empty($this->session->data['order_id'])) 
        {
             //Limpa a sessão
            $this->cart->clear();
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);    
            unset($this->session->data['coupon']);
        }

        $this->response->setOutput($output);
	}
  
    private function get_url()
    {
        $tipo_conta = $this->config->get('akatus_tipo_conta');

        if ($tipo_conta === 'PRODUCAO') {
            return "https://www.akatus.com/api/v1/carrinho.xml";
        }

        return "https://sandbox.akatus.com/api/v1/carrinho.xml";
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

