<?php 

class ModelPaymentakatusb extends Model 
{
  	public function getMethod() {
        return array(
            'code'         => 'akatusb',
            'title'      => $this->config->get('akatusb_nome'),
            'sort_order' => $this->config->get('akatusb_sort_order')
        );
    }
}