<form action="index.php?route=payment/akatusb/confirm" method="post" enctype="application/x-www-form-urlencoded" name="pagamento" id="pagamento">
    <div id="botao_boleto" style="width:100%; text-align:right">
        <input name="botao_finalizar" type="submit" value="Finalizar e Gerar Boleto" onsumbmit="javascript:send();" class="button" />
    </div>
    <div id="carregando" style="width:100%; text-align:right; display:none">
        <img src="image/carregando.gif" width="220" height="19" />
    </div>
</form>

<script type="text/javascript">

    <?php
		$this->load->model('setting/setting');
        $current_settings = $this->model_setting_setting->getSetting('akatusb');
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

    function send() {
        document.getElementById('botao_boleto').style.display="none";
        document.getElementById('carregando').style.display="block";
    }

</script>
