<div id="bloco_cc" style="width:270px; display:none; height:350px; margin-top:-20px;">
    <div id="bandeiras_akatus">
        <ul id="cartoes_akatus">
            <li>
                <label><img id='cartao_visa'  src='image/akatus/cartao_visa.gif' ><br>
                    <input name='bandeira_cartao' type='radio' value='cartao_visa' />
                </label>
            </li>

            <li>
                <label><img id='cartao_master'  src='image/akatus/cartao_master.gif' ><br>
                    <input name='bandeira_cartao' type='radio' value='cartao_master' />
                </label>
            </li>

            <li>
                <label><img id='cartao_elo'  src='image/akatus/cartao_elo.gif' ><br>
                    <input name='bandeira_cartao' type='radio' value='cartao_elo' />
                </label>
            </li>

            <li>
                <label><img id='cartao_diners'  src='image/akatus/cartao_diners.gif' ><br>
                    <input name='bandeira_cartao' type='radio' value='cartao_diners' />
                </label>
            </li>

            <li>
                <label><img id='cartao_amex'  src='image/akatus/cartao_amex.gif' ><br>
                    <input name='bandeira_cartao' type='radio' value='cartao_amex' />
                </label>
            </li>
            <li style="clear:both;"></li>
        </ul>
    </div>

    <div id="dados_titular_cartao">
        <div id="form_titular_cartao">
                <ul class="form-cartao">
                    <li>
                        <strong>CPF do Titular </strong>
                        <input name="cartao_cpf" id="cartao_cpf" type="text" size="40" maxlength="11"/>
                        <span>*Somente Números</span>
                    </li>
                    <li>
                        <strong>Nome do Titular </strong>
                        <input name="cartao_titular" id="cartao_titular" type="text" size="40" />
                        <span>&nbsp;(como gravado no cart&atilde;o)</span>
                    </li>
                    <li>
                        <strong>N&uacute;mero do Cart&atilde;o </strong>
                        <input name="cartao_numero" id="cartao_numero" type="text" size="40" maxlength="16" />
                    </li>
                    <li>
                        <strong>Validade</strong>
                        <select name="cartao_mes" id="cartao_mes">
                            <option value="">mês</option>
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
                            <option value="">ano</option>
                                <?php
                                    $anos_validade_cartao='';
                                    for($i=date('Y'); $i<=(date('Y')+10); $i++) {
                                        @$anos_validade_cartao .='<option value="'.($i-2000).'">'.$i.'</option>';
                                    }
                                    
                                    echo $anos_validade_cartao;
                                ?>                                    
                        </select>
                    </li>
                    <li>
                        <strong>C&oacute;digo de Seguran&ccedil;a </strong>
                        <input name="cartao_codigo" id="cartao_codigo" type="text" size="10" maxlength="4"/>
                        <span><a href="javascript:mostrar_popup();">O qu&ecirc; &eacute; c&oacute;digo de seguran&ccedil;a? </a> </span>       
                    </li>
                    <li>
                        <strong>Número de Parcelas</strong>
                        <select name="parcelas" style="width:230px">
                            <?php echo $parcelamento ?>
                        </select>
                    </li>
                </ul>

            <div style='position:relative; float:left; border: none; width:100%;' >
                <!-- <div>
                    <table width="800" border="0" cellpadding="3" cellspacing="1">
                        <tr>
                            <td><strong>CPF do Titular </strong></td>
                            <td>&nbsp;</td>
                            <td><input name="cartao_cpf" id="cartao_cpf" type="text" size="40" maxlength="11"/> 
                                Somente números</td>
                        </tr>                        
                        <tr>
                            <td width="163"><strong>Nome do Titular </strong></td>
                            <td width="14">&nbsp;</td>
                            <td width="607"><input name="cartao_titular" id="cartao_titular" type="text" size="60" />
                                &nbsp;(como gravado no cart&atilde;o) </td>
                        </tr>
                        <tr>
                            <td><strong>N&uacute;mero do Cart&atilde;o </strong></td>
                            <td>&nbsp;</td>
                            <td><input name="cartao_numero" id="cartao_numero" type="text" size="60" maxlength="16" />&nbsp;</td>
                        </tr>
                        <tr>
                            <td><strong>Validade</strong></td>
                            <td>&nbsp;</td>
                            <td>
                                <select name="cartao_mes" id="cartao_mes">
                                    <option value="">mês</option>
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
                                    <option value="">ano</option>
                                        <?php
                                            $anos_validade_cartao='';
                                            for($i=date('Y'); $i<=(date('Y')+10); $i++) {
                                                @$anos_validade_cartao .='<option value="'.($i-2000).'">'.$i.'</option>';
                                            }
                                            
                                            echo $anos_validade_cartao;
                                        ?>                                    
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
                            <td><strong>Número de Parcelas</strong></td>
                            <td>&nbsp;</td>
                            <td>
                                <select name="parcelas" style="width:500px">
                                    <?php echo $parcelamento ?>
                                </select>
                            </td>
                        </tr>                        
                    </table>

                </div> -->
            </div>
        </div>

        <div id="popup" class="popup">
            <P><img src="image/fechar.jpg" width="20" height="20" align="absmiddle" /><a style="color:#F00; font-weight:bold" href="javascript:ocultar_popup()">Clique aqui para fechar</a></P>
            <p><strong>O que é o Código de Segurança?</strong><br />
                O código de segurança do cartão de crédito é uma seqüência numérica complementar ao número do cartão. Ele garante a veracidade dos dados de uma transação eletrônica, uma vez que a informação é verificada somente pelo portador do cartão e não consta em nenhum tipo de leitura magnética.</p>
            <p><strong>Onde localizar o código de segurança?</strong></p>
            <p> <img src="image/visa.gif" width="189" height="135" align="left" /><br />
                <strong>Visa / MasterCard / Diners</strong><br />
                O código de segurança dos cartões<br />
                Visa / MasterCard / Diners está localizado no verso do cartão e corresponde aos três últimos dígitos da faixa numérica.<br />
            </p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p><img src="image/amex.gif" width="189" height="124" align="left" /><strong>American Express </strong><br />
                O código de segurança está localizado na parte frontal do cartão American Express e corresponde aos quatro dígitos localizados do lado direito acima da faixa numérica do cartão.</p>

            <p><a style="color:#F00; font-weight:bold" href="javascript:ocultar_popup()">Clique aqui para fechar</a></p>

        </div>
    </div>
</div>