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

#Configuração da loja Opencart
require_once('config.php');
   
#Inicialização
require_once(DIR_SYSTEM . 'startup.php');

#banco de dados 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$query = $db->query("SELECT COUNT( * ) AS `Registros` , `language_id` FROM `" . DB_PREFIX. "order_status` GROUP BY `language_id` ORDER BY `language_id`");

foreach ($query->rows as $reg) {
    $db->query("INSERT INTO `" . DB_PREFIX . "order_status` (`order_status_id`, `language_id`, `name`) VALUES
    (10200, " . $reg['language_id'] . ", 'Aguardando Pagamento'),
    (10201, " . $reg['language_id'] . ", 'Em Analise'),
    (10202, " . $reg['language_id'] . ", 'Aprovado'),
    (10203, " . $reg['language_id'] . ", 'Cancelado'),
    (10204, " . $reg['language_id'] . ", 'Completo'),
    (10205, " . $reg['language_id'] . ", 'Devolvido'),
    (10206, " . $reg['language_id'] . ", 'Estornado');");
}


echo "OK!";

?>

