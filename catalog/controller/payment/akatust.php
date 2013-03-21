<?php

require_once 'akatus_base.php';

class ControllerPaymentAkatust extends AkatusPaymentBaseController {

    public function index() {
        $order_id = $this->saveOrder();
        $order = $this->getOrder($order_id);
        
        $xml = $this->getXML($order);
        $url = $this->getUrl();

        $this->session->data['order_id'] = $order_id;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $akatus = $this->xml2array($response);

        $comment = "Caso não tenha concluído o pagamento através do seu cartão de débito, utilize o link abaixo: \n<br>";
        $comment .= '<a href="' . $akatus['resposta']['url_retorno'] . '" target="_blank">' . $akatus['resposta']['url_retorno'] . '</a>';

        $this->model_checkout_order->update($order_id, Transacao::ID_PROCESSING, $comment, $notify = true);

        $this->cart->clear();
        unset($this->session->data['shipping_method']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);

        $this->redirect($akatus['resposta']['url_retorno']);
    }
}