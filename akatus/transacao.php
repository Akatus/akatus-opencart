<?php

class Transacao
{
    const AGUARDANDO_PAGAMENTO  = 'Aguardando Pagamento';
    const EM_ANALISE            = 'Em Análise';
    const APROVADO              = 'Aprovado';
    const CANCELADO             = 'Cancelado';
    const DEVOLVIDO             = 'Devolvido';
    const COMPLETO              = 'Completo';
    
    const ID_PROCESSING             = 2;
    const ID_FAILED                 = 10;
    
    const ID_AGUARDANDO_PAGAMENTO   = 10200;
    const ID_EM_ANALISE             = 10201;
    const ID_APROVADO               = 10202;
    const ID_CANCELADO              = 10203;
    const ID_COMPLETO               = 10204;
    const ID_DEVOLVIDO              = 10205;
}