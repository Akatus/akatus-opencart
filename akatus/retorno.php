<?php

if(! empty($_POST))
{	
    require_once('../config.php');   
    require_once(DIR_SYSTEM . 'startup.php');
    require_once('transacao.php');
    require_once('../catalog/model/checkout/order.php');

    $registry = new Registry();
    $loader = new Loader($registry);
    $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $registry->set('load', $loader);    
    $registry->set('db', $db);
    $registry->set('model_checkout_order', new ModelCheckoutOrder($registry));
    
    $orderModel = $registry->get('model_checkout_order');
    
    $settings = $db->query("SELECT value FROM " . DB_PREFIX . "setting s where s.key='akatus_token_nip'");
    $tokenNip = $settings->row['value'];
    
    if((! isset($_POST['token'])) || ($_POST['token'] != $tokenNip)) die;

    $orders = $db->query('SELECT * FROM `' . DB_PREFIX . 'order` WHERE order_id = ' . $_POST["referencia"]);
    $order = $orders->row;

    $novoStatus = getNovoStatus($_POST['status'], $order['order_status_id']);

    if ($novoStatus) {
        if ($novoStatus == Transacao::ID_COMPLETO) {
            $orderModel->confirm($order['order_id'], $novoStatus, $notify = true);
        } else {
            $orderModel->update($order['order_id'], $novoStatus, $notify = true);
        }
    }
}


function getNovoStatus($statusRecebido, $statusAtual)
{
    switch ($statusRecebido) {

        case Transacao::AGUARDANDO_PAGAMENTO:
            if ($statusAtual == Transacao::ID_PROCESSING) {
                return Transacao::ID_AGUARDANDO_PAGAMENTO;
            } else {
                return false;
            }

        case Transacao::EM_ANALISE:
            $listaStatus = array(
                Transacao::ID_PROCESSING,
                Transacao::ID_AGUARDANDO_PAGAMENTO
            );            
            
            if (in_array($statusAtual, $listaStatus)) {
                return Transacao::ID_EM_ANALISE;
            } else {
                return false;
            }

        case Transacao::APROVADO:
            $listaStatus = array(
                Transacao::ID_PROCESSING,                
                Transacao::ID_AGUARDANDO_PAGAMENTO,
                Transacao::ID_EM_ANALISE
            );
            
            if (in_array($statusAtual, $listaStatus)) {
                return Transacao::ID_APROVADO;
            }

        case Transacao::CANCELADO:
            $listaStatus = array(
                Transacao::ID_PROCESSING,
                Transacao::ID_AGUARDANDO_PAGAMENTO,
                Transacao::ID_EM_ANALISE
            );
            
            if (in_array($statusAtual, $listaStatus)) {
                return Transacao::ID_CANCELADO;
            }

        case Transacao::COMPLETO:
            $listaStatus = array(
                Transacao::ID_PROCESSING,
                Transacao::ID_AGUARDANDO_PAGAMENTO,
                Transacao::ID_EM_ANALISE,
                Transacao::ID_APROVADO,
            );                

            if (in_array($statusAtual, $listaStatus)) {
                return Transacao::ID_COMPLETO;
            } else {
                return false;
            }            

        case Transacao::DEVOLVIDO:
            $listaStatus = array(
                Transacao::ID_APROVADO,
                Transacao::ID_COMPLETO
            );
            
            if (in_array($statusAtual, $listaStatus)) {
                return Transacao::ID_DEVOLVIDO;                    
            } else {
                return false;
            }            
            
        default:
            return false;
    }
}