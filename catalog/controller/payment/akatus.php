<?php

require_once 'akatus_base.php';

class ControllerPaymentAkatus extends AkatusPaymentBaseController {

    public function index() {
        $orderId = $this->saveOrder();
        $this->session->data['order_id'] = $orderId;

        $db = $this->getDatabaseConnection();
        
        $order = $db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . $orderId ."' LIMIT 1");
        $xml = $this->getXML($order);        
        $url = $this->getUrl();
        
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

        // TODO: atualizar somente de acordo com a resposta

        if ($akatus['resposta']['status'] == 'erro') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=4&msg=" . urlencode($akatus['resposta']['descricao']) . "';</script>";
            $db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = 10 WHERE `order_id` = ' . $this->session->data['order_id']);

        } else if ($akatus['resposta']['status'] == 'Em Análise') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=1';</script>";
            $db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = 10201 WHERE `order_id` = ' . $this->session->data['order_id']);
            
        } else if ($akatus['resposta']['status'] == 'Cancelado') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=2';</script>";
            $db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = 10203 WHERE `order_id` = ' . $this->session->data['order_id']);
            
        } else if ($akatus['resposta']['status'] == 'Aprovado') {
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=3';</script>";
            $db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = 10202 WHERE `order_id` = ' . $this->session->data['order_id']);
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