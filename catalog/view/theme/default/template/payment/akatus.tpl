<?php

function parcelar($valorTotal, $taxa, $nParcelas)
{
    $taxa = $taxa/100;
    $cadaParcela = ($valorTotal*$taxa)/(1-(1/pow(1+$taxa, $nParcelas)));
    return round($cadaParcela, 2);
}

#Seleciona dados do pedido

        $registry = new Registry();
		
		// Loader
		$loader = new Loader($registry);
		$registry->set('load', $loader);
		
		// Config
		$config = new Config();
		$registry->set('config', $config);

		
		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$registry->set('db', $db);
		$pedido = $db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '".$this->session->data['order_id']."'");
        
        
        $valor=number_format($pedido->row['total'], 2, '.', '');

#define a quantidade máxima de parcelas

$maximo_parcelas=$this->config->get('akatus_limite_parcelas');

if($valor>5) 
{
    $splitss = (int) ($valor/5);
    
    if($splitss<=$maximo_parcelas)
    {
        $total_parcelas = $splitss;
    }
    else
    {
        $total_parcelas = $maximo_parcelas;
    }
}
else
{
    $total_parcelas = 1;
}

#Quantidade de parcelas sem juros
$semjuros=$this->config->get('akatus_sem_juros');

#valor padrão dos juros Akatus
$juros=1.99;
        
#calcula o parcelamento de acordo com o valor do pedido. A parcela mínima da Akatus é de 5 reais


$parcelamento='<UL id="lista_de_parcelas">';
	
	
	for($j=1; $j<=$total_parcelas;$j++) 
	{
	
		if($semjuros>=$j) 
		{
		
			$parcelas = $valor/$j;
			$parcelas = number_format($parcelas, 2, '.', '');
			
			$parcelamento .= '<option value="'.$j.'">'.$j.'x de R$'.number_format($parcelas, 2,',', '.').' Sem Juros</option>';
			
			
			
		}
		else
		{
		
			$parcelas = parcelar($valor, $juros, $j);
			$parcelas = number_format($parcelas, 2, '.', '');
			
			$parcelamento .= '<option value="'.$j.'">'.$j.'x de R$'.number_format($parcelas, 2,',', '.').' Com Juros de'.number_format($juros, 2, ',', ',').'% A.M.</option>
			
			';
			
			
		}
	
	}
	
	$parcelamento .='</UL>';



?>


		
		<script type="text/javascript" src="catalog/view/javascript/validacao.js"></script>
		<link href="akatus/estilos.css" rel="stylesheet" type="text/css" />
		
		
		
		<form action="index.php?route=payment/akatus/confirm" method="post" enctype="application/x-www-form-urlencoded" name="pagamento" id="pagamento" >
		
		<div style="width:100%; display:block; height:750px;">
		
		<div id="bandeiras_akatus">
		
		<div class="checkout-heading">1) Selecione abaixo a bandeira do seu cartão:</div>
		
		<UL id="cartoes_akatus">
        
        <LI><LABEL><img id='cartao_visa'  src='image/akatus/cartao_visa.gif' ><BR>
			
			<input name='bandeira_cartao' type='radio' value='cartao_visa' checked="checked" />
			
			</label></LI>
            
            
        <LI><label><img id='cartao_master'  src='image/akatus/cartao_master.gif' >
			<BR>
			
			<input name='bandeira_cartao' type='radio' value='cartao_master' />
			
			</label>
            
			</LI>
            
            
            
         <LI><label><img id='cartao_elo'  src='image/akatus/cartao_elo.gif' >
			
			
			<BR>
			
			<input name='bandeira_cartao' type='radio' value='cartao_elo' />
			
			</label></LI>
            
            
            
            
         <LI><LABEL><img id='cartao_diners'  src='image/akatus/cartao_diners.gif' >
			<BR>
			
			<input name='bandeira_cartao' type='radio' value='cartao_diners' />
			
			</label>
			
			</LI>
            
            
            
            
            <LI><label><img id='cartao_amex'  src='image/akatus/cartao_amex.gif' ><BR>
			
			<input name='bandeira_cartao' type='radio' value='cartao_amex' />
			
			</label></LI>
            
            
            
            </ul></div>
            
            
            <div id="dados_titular_cartao">
	
	<div id="form_titular_cartao">
	
	<div style='position:relative; float:left; margin-top:20px; background-color:white; border: none; width:100%;' >
	<div class="checkout-heading">2) Dados do Titular do Cartão:</div>
	<div style='padding:20px'>
	
	Observação: Os dados do cartão são enviados diretamente para a operadora a fim de autorizar a transação. Esses dados NÃO serão armazenados pela nossa loja.<BR><BR>
	
    
    <?php
    
    #Calcula anos da validade do cartão
    
    $anos_validade_cartao='';
    
    for($i=date('Y'); $i<=(date('Y')+10); $i++)
    {
        @$anos_validade_cartao .='<option value="'.($i-2000).'">'.$i.'</option>';
    }
    
    
    
    ?>
    
    
    
    <table width="800" border="0" cellpadding="3" cellspacing="1">
		  <tr>
			<td width="163"><strong>Nome do Titular </strong></td>
			<td width="14">&nbsp;</td>
			<td width="607"><input name="cartao_titular" id="cartao_titular" type="text" size="60" />
			&nbsp;(como gravado no cart&atilde;o) </td>
		  </tr>
		  <tr>
			<td><strong>N&uacute;mero do Cart&atilde;o </strong></td>
			<td>&nbsp;</td>
			<td><input name="cartao_numero" id="cartao_numero" type="text" size="60" />&nbsp;</td>
		  </tr>
		  <tr>
			<td><strong>Validade</strong></td>
			<td>&nbsp;</td>
			<td>
			  <select name="cartao_mes" id="cartao_mes">
			  <option value="-1">MÊS</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			  </select>
			/ 
			<select name="cartao_ano" id="cartao_ano">
			  <option value="-1">ANO</option>
			  
			 <?php echo $anos_validade_cartao ?>
			</select>
			</td>
		  </tr>
		  <tr>
			<td><strong>C&oacute;digo de Seguran&ccedil;a </strong></td>
			<td>&nbsp;</td>
			<td><input name="cartao_codigo" id="cartao_codigo" type="text" size="10" maxlength="4"/>
		    <a href="javascript:mostrar_popup();">O qu&ecirc; &eacute; c&oacute;digo de seguran&ccedil;a? </a></td>
		  </tr>
		  <tr>
			<td><strong>CPF do Titular </strong></td>
			<td>&nbsp;</td>
			<td><input name="cartao_cpf" id="cartao_cpf" type="text" size="40" maxlength="11"/> 
			Somente números</td>
		  </tr>
		  <tr>
			<td><strong>Telefone</strong></td>
			<td>&nbsp;</td>
			<td>( <input name="cartao_telefone_ddd" id="cartao_telefone_ddd" type="text" size="20" maxlength="2" style="width:20px" /> ) <input name="cartao_telefone" id="cartao_telefone" type="text" size="40" maxlength="9" style="width:80px" /></td>
		  </tr>
		</table>
		
		</div>
		</div>
		
		<BR><BR>
		
		<div id="parcelas_akatus">
		
		<div class="checkout-heading">3) Escolha a opção de Parcelamento do Pedido</div>
		
		<div style="padding:20px; padding-left:160px;">
		<select name="parcelas" style="width:500px"><?php echo $parcelamento ?>
		
		</select>
		</div>
		</div>
		</div>
		
        <div id="div_botao_enviar">
		<CENTER>
		<BR><BR><input name="Botão" id="botao_enviar" type="button" value="Concluir Pagamento" class="button" onclick="if(pagar()){document.getElementById('pagamento').submit();}" />
        </CENTER>
        </div>
        
        <div id="carregando">
        <CENTER>
		<BR><BR><img src="/image/carregando.gif" width="220" height="19" />
        </CENTER>
        </div>
		
		</form>
        
        <script>
            <?php
                $this->load->model('setting/setting');
                $current_settings = $this->model_setting_setting->getSetting('akatus');
                $is_sandbox = $current_settings['akatus_tipo_conta'] != 'PRODUCAO';
                $public_token = isset($current_settings['akatus_public_token']) ? $current_settings['akatus_public_token'] : '';
            ?>

            $.getScript("https://static.akatus.com/js/akatus.min.js",function() {
                var formulario = document.getElementById('pagamento');
                var config = {
                    <?php if ($is_sandbox) { echo "sandbox: true,"; } ?>
                    publicToken: '<?php echo $public_token; ?>'
                };
                Akatus.init(formulario, config);
            });
        </script>

        <div id="popup" class="popup">
<P><img src="/image/fechar.jpg" width="20" height="20" align="absmiddle" /><a style="color:#F00; font-weight:bold" href="javascript:ocultar_popup()">Clique aqui para fechar</a></P>
<p><strong>O que é o Código de Segurança?</strong><br />
O código de segurança do cartão de crédito é uma seqüência numérica complementar ao número do cartão. Ele garante a veracidade dos dados de uma transação eletrônica, uma vez que a informação é verificada somente pelo portador do cartão e não consta em nenhum tipo de leitura magnética.</p>
<p><strong>Onde localizar o código de segurança?</strong></p>
<p> <img src="/image/visa.gif" width="189" height="135" align="left" /><br />
  <strong>Visa / MasterCard / Diners</strong><br />
  O código de segurança dos cartões<br />
  Visa / MasterCard / Diners está localizado no verso do cartão e corresponde aos três últimos dígitos da faixa numérica.<br />
  </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><img src="/image/amex.gif" width="189" height="124" align="left" /><strong>American Express </strong><br />
  O código de segurança está localizado na parte frontal do cartão American Express e corresponde aos quatro dígitos localizados do lado direito acima da faixa numérica do cartão.</p>
  
  <P><a style="color:#F00; font-weight:bold" href="javascript:ocultar_popup()">Clique aqui para fechar</a></P>
  
  </div>
