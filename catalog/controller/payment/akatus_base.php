<?php

require_once(DIR_SYSTEM . '../akatus/transacao.php');

class AkatusPaymentBaseController extends Controller {

	public function __construct($registry) {
		$this->registry = $registry;
        
        $this->language->load('checkout/checkout');
        $this->load->model('affiliate/affiliate');
        $this->load->model('setting/extension');
        $this->load->model('checkout/order');        
	}
    
    protected function saveOrder() {
        $total_data = array();
        $sort_order = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

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

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);

        $data = array();

        $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $data['store_id'] = $this->config->get('config_store_id');
        $data['store_name'] = $this->config->get('config_name');

        if ($data['store_id']) {
            $data['store_url'] = $this->config->get('config_url');
        } else {
            $data['store_url'] = HTTP_SERVER;
        }

        $data['customer_id'] = $this->customer->getId();
        $data['customer_group_id'] = $this->customer->getCustomerGroupId();
        $data['firstname'] = $this->customer->getFirstName();
        $data['lastname'] = $this->customer->getLastName();
        $data['email'] = $this->customer->getEmail();
        $data['telephone'] = $this->customer->getTelephone();
        $data['fax'] = $this->customer->getFax();

        $payment_address = $this->session->data['payment_address'];

        $data['fax']                        = '';
        $data['comment']                    = '';        
        $data['payment_company']            = '';
        $data['payment_company_id']         = '';
        $data['payment_tax_id']             = '';
        $data['payment_address_format']     = '';
        $data['payment_firstname']          = $payment_address['firstname'];
        $data['payment_lastname']           = $payment_address['lastname'];
        $data['payment_address_1']          = $payment_address['address_1'];
        $data['payment_address_2']          = $payment_address['address_2'];
        $data['payment_city']               = $payment_address['city'];
        $data['payment_postcode']           = $payment_address['postcode'];
        $data['payment_zone_id']            = $payment_address['zone_id'];
        $data['payment_country_id']         = $payment_address['country_id'];
        
        $data['payment_zone']               = isset($payment_address['zone']) ? $payment_address['zone'] : '';
        $data['payment_country']            = isset($payment_address['country']) ? $payment_address['country'] : '';


        $paymentMethod = $this->request->post['payment_method'];
        $this->load->model('payment/' . $paymentMethod);
        $selectedPaymentMethod = $this->{'model_payment_' . $paymentMethod}->getMethod();

        $data['payment_method'] = $selectedPaymentMethod['title'];
        $data['payment_code'] = $selectedPaymentMethod['code'];


        if ($this->cart->hasShipping()) {
            $shipping_address = $this->session->data['shipping_address'];

            $data['shipping_firstname']         = $shipping_address['firstname'];
            $data['shipping_lastname']          = $shipping_address['lastname'];
            $data['shipping_address_1']         = $shipping_address['address_1'];
            $data['shipping_address_2']         = $shipping_address['address_2'];
            $data['shipping_city']              = $shipping_address['city'];
            $data['shipping_postcode']          = $shipping_address['postcode'];
            $data['shipping_country_id']        = $shipping_address['country_id'];            
            $data['shipping_zone_id']           = $shipping_address['zone_id'];

            $data['shipping_zone']              = isset($shipping_address['zone']) ? $shipping_address['zone'] : '';
            $data['shipping_address_format']    = isset($shipping_address['address_format']) ? $shipping_address['address_format'] : '';
            $data['shipping_country']           = isset($shipping_address['country']) ? $shipping_address['country'] : '';
            $data['shipping_company']        = isset($shipping_address['shipping_company']) ? $shipping_address['shipping_company'] : '';
            $data['shipping_company_id']        = isset($shipping_address['shipping_company_id']) ? $shipping_address['shipping_company_id'] : '';
            $data['shipping_tax_id']        = isset($shipping_address['shipping_tax_id']) ? $shipping_address['shipping_tax_id'] : '';

            $shippingCodeParts = explode('.', $this->request->post['shipping_method']);
            $shippingCode = $shippingCodeParts[0];
            $shippingMethod = $this->session->data['shipping_methods'][$shippingCode];

            $data['shipping_method'] = $shippingMethod['title'];
            $data['shipping_code'] = $shippingCode;
            
        } else {
            $data['shipping_firstname'] = '';
            $data['shipping_lastname'] = '';
            $data['shipping_company'] = '';
            $data['shipping_address_1'] = '';
            $data['shipping_address_2'] = '';
            $data['shipping_city'] = '';
            $data['shipping_postcode'] = '';
            $data['shipping_zone'] = '';
            $data['shipping_zone_id'] = '';
            $data['shipping_country'] = '';
            $data['shipping_country_id'] = '';
            $data['shipping_tax_id'] = '';
            $data['shipping_company_id'] = '';
            $data['shipping_address_format'] = '';
            $data['shipping_method'] = '';
            $data['shipping_code'] = '';
        }

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
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $value,
                    'type' => $option['type']
                );
            }

            $product_data[] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward']
            );
        }

        // Gift Voucher
        $voucher_data = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $voucher_data[] = array(
                    'description' => $voucher['description'],
                    'code' => substr(md5(mt_rand()), 0, 10),
                    'to_name' => $voucher['to_name'],
                    'to_email' => $voucher['to_email'],
                    'from_name' => $voucher['from_name'],
                    'from_email' => $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message' => $voucher['message'],
                    'amount' => $voucher['amount']
                );
            }
        }

        $data['products'] = $product_data;
        $data['vouchers'] = $voucher_data;
        $data['totals'] = $total_data;
        $data['total'] = $total;

        if (isset($this->request->cookie['tracking'])) {
            $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
            $subtotal = $this->cart->getSubTotal();

            if ($affiliate_info) {
                $data['affiliate_id'] = $affiliate_info['affiliate_id'];
                $data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
            } else {
                $data['affiliate_id'] = 0;
                $data['commission'] = 0;
            }
        } else {
            $data['affiliate_id'] = 0;
            $data['commission'] = 0;
        }

        $data['language_id'] = $this->config->get('config_language_id');
        $data['currency_id'] = $this->currency->getId();
        $data['currency_code'] = $this->currency->getCode();
        $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
        $data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
            $data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
            $data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $data['accept_language'] = '';
        }

        return $this->model_checkout_order->addOrder($data);
    }
    
    protected function getOrder($orderId) {
        $order = $this->model_checkout_order->getOrder($orderId);
        
        $orderProducts = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = " . $orderId);
        $shippingValue = $this->db->query("SELECT value FROM `" . DB_PREFIX . "order_total` WHERE order_id = " . $orderId . " AND code = 'shipping'");
        $coupon = $this->db->query("SELECT value FROM `" . DB_PREFIX . "order_total` WHERE order_id = " . $orderId . " AND code = 'coupon'");
        $voucher = $this->db->query("SELECT value FROM `" . DB_PREFIX . "order_total` WHERE order_id = " . $orderId . " AND code = 'voucher'");
        
        $order['order_products'] = $orderProducts->rows;
        $order['shipping_value'] = (empty($shippingValue->row) ? 0 : $shippingValue->row['value']);
        $order['coupon'] = (empty($coupon->row) ? 0 : $coupon->row);
        $order['voucher'] = (empty($voucher->row) ? 0 : $voucher->row);
        
        return $order;
    }    
    
    protected function getXML($order) {
        $paymentCode = $order['payment_code'];
        $meioDePagamento = null;
        
        switch ($paymentCode) {
            case 'akatusb':
                $meioDePagamento = 'boleto';
                break;

            case 'akatust':
                $meioDePagamento = $_POST['tef'];
                break;

            case 'akatus':
                $meioDePagamento = $_POST['bandeira_cartao'];
                break;
            
            default:
                break;
        }

        $desconto = 0;
        $descontoCoupon = 0;
        $descontoVoucher = 0;

        if ($order['coupon']) {
            $descontoCoupon = abs($order['coupon']['value']);
        }

        if ($order['voucher']) {
            $descontoVoucher = abs($order['voucher']['value']);
        }

        $desconto = number_format($descontoCoupon + $descontoVoucher, 2, '.', '');

        $xml = '<?xml version="1.0" encoding="utf-8"?><carrinho>
          <recebedor>
              <api_key>' . $this->config->get('akatus_api_key') . '</api_key>
              <email>' . $this->config->get('akatus_email_conta') . '</email>
          </recebedor>
          
          <pagador>
              <nome>' . $order['firstname'] . ' ' . $order['lastname'] . '</nome>
              <email>' . $order['email'] . '</email>
				<enderecos>
		            <endereco>
		                <tipo>entrega</tipo>
		                <logradouro>'.$order['payment_address_1'].'</logradouro>
                        <bairro>'.utf8_decode($order['payment_address_2']).'</bairro>
		                <cidade>'.utf8_decode($order['payment_city']).'</cidade>
		                <estado>'.$order['payment_zone_code'].'</estado>
		                <pais>'.$order['payment_iso_code_3'].'</pais>
		                <cep>'.$order['payment_postcode'].'</cep>
		            </endereco>
		        </enderecos>

              <telefones>
                  <telefone>
                      <tipo>residencial</tipo>
                      <numero>' . substr(preg_replace("/[^0-9]/", "", $order['telephone']), 0, 11) . '</numero>
                  </telefone>
              </telefones>
          </pagador>
          
          <produtos>';

        foreach ($order['order_products'] as $order_product) {
            $valor_produto = number_format($order_product['price'], 2, '.', '');
            
            $xml .= '<produto>
                         <codigo>' . $order_product['product_id'] . '</codigo>
                         <descricao>' . $order_product['name'] . '</descricao>
                         <quantidade>' . $order_product['quantity'] . '</quantidade>
                         <preco>' . $valor_produto . '</preco>
                         <peso>0.00</peso>
                         <frete>0.00</frete>
                         <desconto>0.00</desconto>
                     </produto>';           
        }

        $fingerprint_akatus = isset($_POST['fingerprint_akatus']) ? $_POST['fingerprint_akatus'] : '';
        $fingerprint_partner_id = isset($_POST['fingerprint_partner_id']) ? $_POST['fingerprint_partner_id'] : '';

        $xml .= '</produtos>
              
          <transacao>

            <fingerprint_akatus>' . $fingerprint_akatus . '</fingerprint_akatus>
            <fingerprint_partner_id>' . $fingerprint_partner_id . '</fingerprint_partner_id>

            <referencia>' . ($order['order_id']) . '</referencia>
            <meio_de_pagamento>' . $meioDePagamento . '</meio_de_pagamento>

            <desconto>' . $desconto . '</desconto>
            <peso>0.00</peso>
            <frete>' . number_format($order['shipping_value'], 2, '.', '') . '</frete>
            <moeda>BRL</moeda>';
            
        $cartaoCredito = ($paymentCode === 'akatus');
        
        if ($cartaoCredito) {
            $xml .=	'<numero>' . $_POST['cartao_numero'] . '</numero>
                <parcelas>'. $_POST['parcelas'].'</parcelas>
                <codigo_de_seguranca>'. $_POST['cartao_codigo'] . '</codigo_de_seguranca>
                <expiracao>' . $_POST['cartao_mes'] . '/' . $_POST['cartao_ano'] . '</expiracao>

                <portador>
                    <nome>' . strtoupper($_POST['cartao_titular']) . '</nome>
                    <cpf>' . preg_replace("/[^0-9]/","",$_POST['cartao_cpf']) . '</cpf>
                    <telefone>' . substr(preg_replace("/[^0-9]/", "", $order['telephone']), 0, 11) . '</telefone>
			</portador>';
        }
		
        $xml .= '</transacao></carrinho>';
        
        return utf8_encode($xml);
    }

    protected function getUrl($payment_method) {
		$this->load->model('setting/setting');
        $current_settings = $this->model_setting_setting->getSetting($payment_method);
        $is_sandbox = $current_settings['akatus_tipo_conta'] != 'PRODUCAO';

        if ($is_sandbox) {
            return "https://sandbox.akatus.com/api/v1/carrinho.xml";
        }

        return "https://www.akatus.com/api/v1/carrinho.xml";
    }
    
    protected function clearSession() {
        $this->cart->clear();
        unset($this->session->data['shipping_method']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);        
        unset($this->session->data['vouchers']);        
    }
    
    protected function xml2array($contents, $get_attributes = 1, $priority = 'tag') {
        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
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

        if (!$xml_values)
            return; //Hmm... 

            
//Initializations 
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference 
        //Go through the tags. 
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array 
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble 
            //This command will extract these variables into the foreach scope 
            // tag(string), type(string), level(int), attributes(array). 
            extract($data); //We could use the array by itself, but this cooler. 

            $result = array();
            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
            }

            //Set the attributes too. 
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
                }
            }

            //See tag status and do the needed. 
            if ($type == "open") {//The starting of the tag '<tag>' 
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                } else { //There was another element with the same tag name 
                    if (isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together 
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array 
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />' 
                //See if the key is already taken. 
                if (!isset($current[$tag])) { //New Key 
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array) 
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 
                        // ...push the new element into that array. 
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else { //If it is not an array... 
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value 
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken 
                    }
                }
            } elseif ($type == 'close') { //End of tag '</tag>' 
                $current = &$parent[$level - 1];
            }
        }

        return($xml_array);
    }    
}
