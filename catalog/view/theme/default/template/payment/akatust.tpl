<form action="#" method="post" enctype="application/x-www-form-urlencoded" name="pagamento" id="pagamento">

    <div style="width:100%;" id="escolher_banco">
        <table width="609" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td width="154" align="center" valign="middle"><img src="image/banco_itau.jpg" width="168" height="50" />&nbsp;</td>
                <td width="239" align="center" valign="middle"><input name="botao_finalizar2" type="button" value="Finalizar e Efetuar Pagamento" onclick="javascript:send('tef_itau');" class="button" /></td>
                <td width="216" rowspan="3" align="center" valign="middle">Atenção: Pode ser necessário desabilitar seu bloqueador popup</td>
            </tr>
            <tr>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle"><img src="image/banco_bradesco.jpg" width="168" height="50" /></td>
                <td align="center" valign="middle"><input name="botao_finalizar" type="button" value="Finalizar e Efetuar Pagamento" onclick="javascript:send('tef_bradesco');" class="button" /></td>
            </tr>
            <tr>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
                <td align="center" valign="middle">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle"><img src="image/banco_bb.jpg" alt="banco do brasil" width="168" height="50" /></td>
                <td align="center" valign="middle"><input name="botao_finalizar3" type="button" value="Finalizar e Efetuar Pagamento" onclick="javascript:send('tef_bb');" class="button" /></td>
                <td align="center" valign="middle">&nbsp;</td>
            </tr>
        </table>

    </div>

    <div id="carregando" style="width:100%; text-align:right; display:none">
        <BR /><BR />
        <center>
            <img src="image/carregando.gif" width="220" height="19" />
        </center>
    </div>
</form>

<script type="text/javascript">

    <?php
        $this->load->model('setting/setting');
        $current_settings = $this->model_setting_setting->getSetting('akatust');
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

    function send(banco) {
        document.getElementById('escolher_banco').style.display="none";
        document.getElementById('carregando').style.display="block";

        url = 'index.php?route=payment/akatust/confirm&tef='+banco;
        $('#pagamento').attr('action', url);
        $('#pagamento').submit();
    }

</script>

