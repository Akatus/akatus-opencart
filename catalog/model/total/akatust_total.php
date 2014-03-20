<?php 

/**
 * @author Akatus
 * @copyright 
 * @site http://www.akatus.com.br
 * @version 1.0
 **/

class ModelTotalakatustTotal extends Model 
{
  public function getTotal(&$total_data, &$total, &$taxes) {
    $paymethod = false;
    if(isset($this->session->data['payment_method']['code']))
        $paymethod = $this->session->data['payment_method']['code'];

    $enabled = false;
    if(!empty($this->config->get('akatust_total_status')))
      $enabled = $this->config->get('akatust_total_status');

    $discount = 0;
    if(!empty($this->config->get('akatust_discount')))
      $discount = $this->config->get('akatust_discount');

    if($paymethod == 'akatust' && !empty($enabled) && !empty($discount)){

      $this->load->language('total/akatust_total');

      $percent = $this->config->get('akatust_discount') / 100;
      $percent = $total * $percent;
      $total_data[] = array( 
      'code'     => 'akatust',
            'title'      => $this->language->get('discount') . ' ' . $discount. '%',
            'text'       => '<span class="discount">- ' . $this->currency->format($percent) . '</span>',
            'value'      => $percent*-1,
            'sort_order' => $this->config->get('akatust_total_sort_order')
      );
      $total -= $percent;
    }
  }
}
?>
