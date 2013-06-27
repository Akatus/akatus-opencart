<?php

class ModelPaymentAkatus extends Model {

    public function getMethod() {
        return array(
            'code'         => 'akatus',
            'title'      => $this->config->get('akatus_nome'),
            'sort_order' => $this->config->get('akatus_sort_order')
        );
  	}
}