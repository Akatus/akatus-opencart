<script type="text/javascript">
<!--
window.name='loja';

function vai()
{
	document.getElementById('botao_boleto').style.display="none";
	document.getElementById('carregando').style.display="block";
	window.location = 'index.php?route=payment/akatusb/confirm';
	
	return true;
}
//-->
</script>

<div id="botao_boleto" style="width:100%; text-align:right"><input name="botao_finalizar" type="button" value="Finalizar e Gerar Boleto" onclick="javascript:vai();" class="button" /></div>

<div id="carregando" style="width:100%; text-align:right; display:none">
     
	<img src="image/carregando.gif" width="220" height="19" />
       
 </div>
