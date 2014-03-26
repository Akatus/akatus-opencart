<?php 

/**
 * @author Akatus
 * @copyright 
 * @site http://www.akatus.com.br
 * @version 1.0
 **/

class ModelTotalakatusbTotal extends Model 
{
  public function getTotal(&$total_data, &$total, &$taxes) {
    $paymethod = false;
    if(isset($this->session->data['payment_method']['code']))
        $paymethod = $this->session->data['payment_method']['code'];

    $enabled = false;
    $akatusb_total_status = $this->config->get('akatusb_total_status');
    if(!empty($akatusb_total_status))
      $enabled = $this->config->get('akatusb_total_status');

    $discount = 0;
    $akatusb_discount = $this->config->get('akatusb_discount');
    if(!empty($akatusb_discount))
      $discount = $akatusb_discount;

    if($paymethod == 'akatusb' && !empty($enabled) && !empty($discount)){

      $this->load->language('total/akatusb_total');

      $percent = $akatusb_discount / 100;
      $percent = $total * $percent;
      $total_data[] = array( 
      'code'     => 'akatusb',
            'title'      => $this->language->get('discount') . ' ' . $discount. '%',
            'text'       => '<span class="discount">- ' . $this->currency->format($percent) . '</span>',
            'value'      => $percent*-1,
            'sort_order' => $this->config->get('akatusb_total_sort_order')
      );
      $total -= $percent;
    }
  }
}
?>
