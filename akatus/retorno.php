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
 * @version 1.0 Beta
 **/

#Fecha a janela se tentarem acessar diretamente este arquivo
if(empty($_POST))
{	
	echo '<script type="text/javascript">window.close()</script>';
	exit;
}

#Arquivo com as configurações da loja
require_once('../config.php');   

#rotinas Inicialização
require_once(DIR_SYSTEM . 'startup.php');

global $db;

#Cria nova conexão com o banco de dados e seleciona o TOKEN da Akatus
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$query = $db->query("SELECT value FROM " . DB_PREFIX . "setting s where s.key='akatus_token_nip'");

foreach ($query->rows as $setting) 
{
	define('TOKEN', $setting['value']);
}

#O Token enviado via POST deve ser igual ao da configuração da loja, senão 
#a execução do programa será encerrada

if(TOKEN !=$_POST['token']) die;

	
#Seleciona os dados da compra
$order = $db->query('SELECT * FROM `' . DB_PREFIX . 'order` WHERE order_id = ' . $_POST["referencia"]);


switch($_POST['status'])
{
	case 'Aguardando Pagamento' :
		$order_status_id = 10200;
	break;
		
	case 'Em Analise' :
	case 'Em AnÃ¡lise':
		$order_status_id = 10201;
	break;
		
	case 'Aprovado' :
		$order_status_id = 10202;
	break;
		
	case 'Cancelado' :
		$order_status_id = 10203;
	break;
	
	default:
		$order_status_id = 10206;
	break;
}

#Atualiza o status da compra

$db->query('UPDATE `' . DB_PREFIX . 'order` SET `order_status_id` = ' . $order_status_id . ' WHERE `order_id` = ' . $_POST['referencia']);
$db->query("INSERT INTO `" . DB_PREFIX . "order_history` VALUES (NULL , '" . $_POST['referencia'] . "', '" . $order_status_id . "', '0', '', NOW());");	

?>