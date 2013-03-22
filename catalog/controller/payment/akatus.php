<?php

require_once 'akatus_base.php';

class ControllerPaymentAkatus extends AkatusPaymentBaseController {

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

        if ($akatus['resposta']['status'] == 'erro') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=4&msg=" . urlencode($akatus['resposta']['descricao']) . "';</script>";            
            $this->model_checkout_order->confirm($order_id, Transacao::ID_FAILED, $comment = '', $notify = false);

        } else if ($akatus['resposta']['status'] == 'Em Análise') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=1';</script>";     
            $this->model_checkout_order->confirm($order_id, Transacao::ID_EM_ANALISE, $comment = '', $notify = true);
            
        } else if ($akatus['resposta']['status'] == 'Cancelado') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=2';</script>";            
            $this->model_checkout_order->confirm($order_id, Transacao::ID_CANCELADO, $comment = '', $notify = true);
            
        } else if ($akatus['resposta']['status'] == 'Aprovado') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=3';</script>";
            $this->model_checkout_order->confirm($order_id, Transacao::ID_APROVADO, $comment = '', $notify = true);
        }

        $this->cart->clear();
        unset($this->session->data['shipping_method']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['order_id']);
        unset($this->session->data['comment']);
        unset($this->session->data['coupon']);

        $this->response->setOutput($ouput);
    }
}