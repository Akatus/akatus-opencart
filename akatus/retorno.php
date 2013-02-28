<?php
/*
+---------------------------------------------------+
| 			 MÓDULO DE PAGAMENTO AKATUS 			|
|---------------------------------------------------|
|													|
|  Este módulo permite receber pagamentos através   |
|  do gateway de pagamentos Akatus em lojas			|
|  utilizando a plataforma Prestashop				|
|													|
|---------------------------------------------------|
|													|
|  Desenvolvido por: www.andresa.com.br				|
|					 contato@andresa.com.br			|
|													|
+---------------------------------------------------+
*/

/**
 * @author Andresa Martins da Silva
 * @copyright Andresa Web Studio
 * @site http://www.andresa.com.br
 **/

class Transacao
{
    const AGUARDANDO_PAGAMENTO  = 'Aguardando Pagamento';
    const EM_ANALISE            = 'Em Análise';
    const APROVADO              = 'Aprovado';
    const CANCELADO             = 'Cancelado';
    const DEVOLVIDO             = 'Devolvido';
    const COMPLETO              = 'Completo';
    
    const ID_PROCESSING             = 2;
    
    const ID_AGUARDANDO_PAGAMENTO   = 10200;
    const ID_EM_ANALISE             = 10201;
    const ID_APROVADO               = 10202;
    const ID_CANCELADO              = 10203;
    const ID_COMPLETO               = 10204;
    const ID_DEVOLVIDO              = 10205;
}

if(! empty($_POST))
{	
    require_once('../config.php');   
    require_once(DIR_SYSTEM . 'startup.php');

    global $db;

    $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $settings = $db->query("SELECT value FROM " . DB_PREFIX . "setting s where s.key='akatus_token_nip'");
    $tokenNip = $settings->row['value'];
    
    if($tokenNip != $_POST['token']) die;

    $orders = $db->query('SELECT * FROM `' . DB_PREFIX . 'order` WHERE order_id = ' . $_POST["referencia"]);
    $order = $orders->row;

    $novoStatus = getNovoStatus($_POST['status'], $order['order_status_id']);

    if ($novoStatus) {
        $db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = ' . $novoStatus . ' WHERE `order_id` = ' . $order['order_id']);
        $db->query("INSERT INTO `" . DB_PREFIX . "order_history` VALUES (NULL , '" . $order['order_id'] . "', '" . $novoStatus . "', '0', '', NOW());");	    
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