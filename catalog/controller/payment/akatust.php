<?php

require_once 'akatus_base.php';

class ControllerPaymentAkatust extends AkatusPaymentBaseController {

    public function index() {
        $order_id = $this->saveOrder();
        $order = $this->getOrder($order_id);
        
        $xml = $this->getXML($order);
        $url = $this->getUrl();

        $this->clearSession();
        
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
            $this->model_checkout_order->confirm($order_id, Transacao::ID_FAILED, $comment = '', $notify = false);
            
            $ouput = "<script>window.location = 'index.php?route=information/akatus&tipo=4&msg=" . urlencode($akatus['resposta']['descricao']) . "';</script>";
            $this->response->setOutput($ouput);
            
        } else {
            $comment = "Caso não tenha concluído o pagamento através do seu cartão de débito, utilize o link abaixo: \n<br>";
            $comment .= '<a href="' . $akatus['resposta']['url_retorno'] . '" target="_blank">' . $akatus['resposta']['url_retorno'] . '</a>';

            $this->model_checkout_order->confirm($order_id, $this->config->get('akatust_padrao'), $comment, $notify = true);

            $this->redirect($akatus['resposta']['url_retorno']);
        }
    }
}