<?php 

class ModelPaymentakatust extends Model {
  	public function getMethod() {
        return array(
            'code'         => 'akatust',
            'title'      => $this->config->get('akatust_nome'),
            'sort_order' => $this->config->get('akatust_sort_order')
        );
    }
}