<?php

require_once('config.php');
require_once(DIR_SYSTEM . 'startup.php');

function missing_order_status($db) {
    $result = $db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id >= 10200");

    if (empty($result->rows)) {
        return true;
    }

    return false;
}



$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (missing_order_status($db)) {

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

    echo "Os status das transacoes da Akatus foram inseridos com sucesso!\n";

} else {

    header("HTTP/1.0 404 Not Found");

}

$db->query("CREATE TABLE IF NOT EXISTS `akatus_transacoes` (
    `id` INT NULL AUTO_INCREMENT,
    `id_pedido` INT NOT NULL,
    `id_akatus` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY IDX_ID_PEDIDO (`id_pedido`))
");

echo "A tabela para armazenar os IDs de transações da Akatus foi criada com sucesso!\n";

?>
