var logged = $('#logged').val();
var shipping_required = $('#shipping_required').val() === 'true';
var email_pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
var telephone_pattern = /[0-9]{10,11}/;

var MSG_NOME_INVALIDO = 'Por favor, preencha o campo Nome.';
var MSG_SOBRENOME_INVALIDO = 'Por favor, preencha o campo Sobrenome.';
var MSG_EMAIL_INVALIDO = 'Por favor, preencha corretamente o campo E-Mail.';
var MSG_TELEFONE_INVALIDO = 'Por favor, preencha corretamente o campo Telefone (de 10 a 11 dígitos, incluindo DDD).';
var MSG_SENHA_INVALIDA = 'Por favor, preencha corretamente o campo Senha (de 4 a 20 caracteres).';
var MSG_CONFIRMACAO_SENHA_INVALIDA = 'Por favor, preencha corretamente o campo Confirmação da Senha (igual a Senha).';
var MSG_ENDERECO_INVALIDO = 'Por favor, preencha o campo Endereço.';
var MSG_BAIRRO_INVALIDO = 'Por favor, preencha o campo Bairro.';
var MSG_CIDADE_INVALIDA = 'Por favor, preencha o campo Cidade.';
var MSG_CEP_INVALIDO = 'Por favor, preencha o campo CEP (somente os 8 dígitos).';
var MSG_PAIS_INVALIDO = 'Por favor, escolha um país.';
var MSG_ESTADO_INVALIDO = 'Por favor, escolha um estado.';
var MSG_METODO_ENTREGA_INVALIDO = 'Por favor, selecione o Método de Entrega.';
var MSG_BANCO_TEF_INVALIDO = 'Por favor, selecione o banco relacionado ao TEF.';
var MSG_MEIO_PAGAMENTO_INVALIDO = 'Por favor, selecione o Meio de Pagamento.';

var MSG_NUMERO_CARTAO_INVALIDO = "Por favor, informe corretamente o número do cartão de crédito.";
var MSG_CODIGO_SEG_INVALIDO_3 = "Atenção, o Código de Segurança deve ter 3 dígitos.";
var MSG_CODIGO_SEG_INVALIDO_4 = "Atenção, o Código de Segurança deve ter 4 dígitos.";

var AKATUS_CARTAO_CREDITO = 'akatus';
var AKATUS_BOLETO = 'akatusb';
var AKATUS_TEF = 'akatust';

function atualizaListaPaises(select) {
    if (this.value == '')
        return;

    $.ajax({
        url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
        dataType: 'json',
        
        beforeSend: function() {
            $(this).after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
                
        complete: function() {
            $('.wait').remove();
        },
                
        success: function(json) {
            var html = '<option value="">--- Selecione ---</option>';

            if (json['zone'] != '') {

                for (i = 0; i < json['zone'].length; i++) {
                    html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                    if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                        html += ' selected="selected"';
                    }

                    html += '>' + json['zone'][i]['name'] + '</option>';
                }
                
            } else {
                html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
            }

            var element_name = $(select).attr('currentTarget').name;
            
            if (/^shipping/.test(element_name)) {
                $('select[name=shipping_zone_id]').html(html);
            } else {
                $('select[name=zone_id]').html(html);
            }
        },
                
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}

function atualizaParcelamento() {
    $.ajax({
        url: 'index.php?route=checkout/checkout/parcelamento',
        dataType: 'html',

        beforeSend: function() {
        },
        complete: function() {
        },
        success: function(parcelas) {
            $('select[name=parcelas]').empty();
            $('select[name=parcelas]').append(parcelas);
        }
    });
}

function atualizaValores() {
    $.ajax({
        url: 'index.php?route=checkout/checkout/calculate',
        type: 'post',
        data: {
            shipping_method: $(this).val()
        },
        dataType: 'html',

        beforeSend: function() {
        },
        complete: function() {
        },
        success: function(html) {
            $('#total').empty();
            $('#total').append(html);

            atualizaParcelamento();
        }
    });
}

function calculaFrete() {
    var countryId, zoneId;

    if ($('input[name=shipping_address]').is(':checked')) {
        countryId = $('select[name=country_id]').val();
        zoneId = $('select[name=zone_id]').val();
    } else {
        countryId = $('select[name=shipping_country_id]').val();
        zoneId = $('select[name=shipping_zone_id]').val();
    }

    if ((countryId != '') && (zoneId != '')) {
        var url = 'index.php?route=checkout/checkout/findShippingMethods&country_id=' + countryId + '&zone_id=' + zoneId;

        $.getJSON(url, function(shippingMethods) {

            $('#shipping-method .warning').hide();
            $('#shipping-method table').empty();

            $.each(shippingMethods, function(shippingMethodName, shippingMethod) {
                var html = "<tr>";
                html += "<td><input type='radio' id='" + shippingMethod.quote[shippingMethodName].code + "' value='" + shippingMethod.quote[shippingMethodName].code + "' name='shipping_method'></td>";
                html += "<td><label for='" + shippingMethod.quote[shippingMethodName].code + "'>" + shippingMethod.quote[shippingMethodName].title + "</label></td>";
                html += "<td style='text-align: right;'><label for='" + shippingMethod.quote[shippingMethodName].code + "'>" + shippingMethod.quote[shippingMethodName].text + "</label></td>";
                html += "</tr>";

                $('#shipping-method table').append(html);
            });
        });
    }
}

function mudarMeioPagamento() {
    switch ($(this).val()) {

        case AKATUS_CARTAO_CREDITO:
            $("#bloco_cc").show();
            $("#bloco_boleto").hide();
            $("#bloco_tef").hide();
            break;

        case AKATUS_BOLETO:
            $("#bloco_boleto").show();
            $("#bloco_tef").hide();
            $("#bloco_cc").hide();
            break;

        case AKATUS_TEF:
            $("#bloco_tef").show();
            $("#bloco_cc").hide();
            $("#bloco_boleto").hide();
            break;

        default:
            break;
    }
}

$(function() {
    if (! logged) {
        $('#shipping-address').hide();
    }
    
    $('input[name=shipping_method]').live('change', atualizaValores);
    
    $('select[name=zone_id]').change(calculaFrete);
    $('input[name=payment_method]').change(mudarMeioPagamento);
    $('select[name=shipping_zone_id]').change(calculaFrete);
    $('input[name=cartao_numero]').keyup(detectarBandeira);
    
    $('select[name=country_id]').change(atualizaListaPaises);
    $('select[name=country_id]').trigger('change');
    $('select[name=shipping_country_id]').change(atualizaListaPaises);
    $('select[name=shipping_country_id]').trigger('change');

    $('#shipping').change(function() {
        $('#shipping-address').toggle();
    });

    $('input[name=shipping-address]').change(function() {
        $('#shipping-new').toggle();
    });

    $('#one-step-checkout').submit(validaFormulario);

});

function validaFormulario(evento) {
    if (dadosCadastraisValidos() &&
        enderecoCobrancaValido() &&
        enderecoEntregaValido() &&
        metodoEntregaValido(shipping_required) &&
        meioPagamentoValido()) {

        return true;

    } else {
        evento.preventDefault();
        return false;
    }
}

function dadosCadastraisValidos() {
    if (logged) {
        return true;
        
    } else {
        var firstname = $('input[name=firstname]');
        if (firstname.val() === '') {
            alert(MSG_NOME_INVALIDO);
            firstname.focus();
            return false;
        }

        var lastname = $('input[name=lastname]');
        if (lastname.val() === '') {
            alert(MSG_SOBRENOME_INVALIDO);
            lastname.focus();
            return false;
        }

        var email = $('input[name=email]');
        if (email.val() === '' || ! email_pattern.test(email.val())) {
            alert(MSG_EMAIL_INVALIDO);
            email.focus();
            return false;
        }

        var telephone = $('input[name=telephone]');
        if (telephone.val() === '' || ! telephone_pattern.test(telephone.val())) {
            alert(MSG_TELEFONE_INVALIDO);
            telephone.focus();
            return false;
        }

        var password = $('input[name=password]');
        if (password.val() === '' || ((password.val().length < 4) || password.val().length > 20 )) {
            alert(MSG_SENHA_INVALIDA);
            password.focus();
            return false;
        }

        var confirm = $('input[name=confirm]');
        if (confirm.val() === '' || (confirm.val() !== password.val())) {
            alert(MSG_CONFIRMACAO_SENHA_INVALIDA);
            confirm.focus();
            return false;
        }

        return true;
    }
}

function enderecoCobrancaValido() {
    var existing_address = $('input[name=payment_address]:checked').val() === 'existing';

    if (existing_address) {
        return true;

    } else {

        var firstname = $('input[name=firstname]');
        if (firstname.val() === '') {
            alert(MSG_NOME_INVALIDO);
            firstname.focus();
            return false;
        }

        var lastname = $('input[name=lastname]');
        if (lastname.val() === '') {
            alert(MSG_SOBRENOME_INVALIDO);
            lastname.focus();
            return false;
        }

        var address_1 = $('input[name=address_1]');
        if (address_1.val() === '') {
            alert(MSG_ENDERECO_INVALIDO);
            address_1.focus();
            return false;
        }

        var address_2 = $('input[name=address_2]');
        if (address_2.val() === '') {
            alert(MSG_BAIRRO_INVALIDO);
            address_2.focus();
            return false;
        }
        
        var city = $('input[name=city]');
        if (city.val() === '') {
            alert(MSG_CIDADE_INVALIDA);
            city.focus();
            return false;
        }

        var postcode = $('input[name=postcode]');
        if (postcode.val() === '' || (postcode.val().length !== 8)) {
            alert(MSG_CEP_INVALIDO);
            postcode.focus();
            return false;
        }

        var country_id = $('select[name=country_id]');
        if (country_id.val() === '') {
            alert(MSG_PAIS_INVALIDO);
            country_id.focus();
            return false;
        }

        var zone_id = $('select[name=zone_id]');
        if (zone_id.val() === '') {
            alert(MSG_ESTADO_INVALIDO);
            zone_id.focus();
            return false;
        }

        return true;
    }
}

function enderecoEntregaValido() {
    var new_shipping_address = $('input[name=shipping_address]:checked').val() === 'new';
    var different_address;
    
    if (logged) {
        different_address = $('input[name=shipping_address]:checked').val() === 'new';
    } else {
        different_address = $('input[name=shipping_address]').is(':checked');
    }

    if (shipping_required) {
        if (new_shipping_address || different_address) {
            var firstname = $('input[name=shipping_firstname]');
            if (firstname.val() === '') {
                alert(MSG_NOME_INVALIDO);
                firstname.focus();
                return false;
            }

            var lastname = $('input[name=shipping_lastname]');
            if (lastname.val() === '') {
                alert(MSG_SOBRENOME_INVALIDO);
                lastname.focus();
                return false;
            }

            var address_1 = $('input[name=shipping_address_1]');
            if (address_1.val() === '') {
                alert(MSG_ENDERECO_INVALIDO);
                address_1.focus();
                return false;
            }

            var address_2 = $('input[name=shipping_address_2]');
            if (address_2.val() === '') {
                alert(MSG_BAIRRO_INVALIDO);
                address_2.focus();
                return false;
            }

            var city = $('input[name=shipping_city]');
            if (city.val() === '') {
                alert(MSG_CIDADE_INVALIDA);
                city.focus();
                return false;
            }

            var postcode = $('input[name=shipping_postcode]');
            if (postcode.val() === '' || (postcode.val().length !== 8)) {
                alert(MSG_CEP_INVALIDO);
                postcode.focus();
                return false;
            }

            var country_id = $('select[name=shipping_country_id]');
            if (country_id.val() === '') {
                alert(MSG_PAIS_INVALIDO);
                country_id.focus();
                return false;
            }

            var zone_id = $('select[name=shipping_zone_id]');
            if (zone_id.val() === '') {
                alert(MSG_ESTADO_INVALIDO);
                zone_id.focus();
                return false;
            }
        }
    }

    return true;
}

function metodoEntregaValido(shipping_required) {
    var shipping_method_selected = $('input[name=shipping_method]').is(':checked');
    
    if (shipping_method_selected || (! shipping_required)) {
        return true;
    } else {
        alert(MSG_METODO_ENTREGA_INVALIDO);
        return false;        
    }
}

function validacaoTEF() {
    var tef_selected = ('input[name=tef]').is(':checked');
    
    if (tef_selected) {
        return true;
    } else {
        alert(MSG_BANCO_TEF_INVALIDO);
        return false;                
    }
}

function validacaoCartaoCredito() {
    var cpf_titular_cartao = $('input[name=cartao_cpf]');
    if (cpf_titular_cartao.val() === '' || (! cpfValido(cpf_titular_cartao.val()))) {
        alert('Por favor, preencha um CPF válido.');
        cpf_titular_cartao.focus();
        return false;
    }    

    var nome_titular_cartao = $('input[name=cartao_titular]');
    if (nome_titular_cartao.val() === '') {
        alert('Por favor, preencha o nome do titular do cartão.');
        nome_titular_cartao.focus();
        return false;
    }

    if (! cartaoValido()) {
        return false;
    }

    var cartao_mes = $('select[name=cartao_mes]');
    if (cartao_mes.val() === '') {
        alert('Por favor, informe o mês de validade do cartão de crédito.');
        cartao_mes.focus();
        return false;
    }

    var cartao_ano = $('select[name=cartao_ano]');
    if (cartao_ano.val() === '') {
        alert('Por favor, informe o ano de validade do cartão de crédito.');
        cartao_ano.focus();
        return false;
    }    
    
    return true;
}

function meioPagamentoValido() {
    var payment_method_selected = $('input[name=payment_method]').is(':checked');
    var payment_method = $('input[name=payment_method]:checked').val();

    if (payment_method_selected) {
        
        switch (payment_method) {

            case AKATUS_CARTAO_CREDITO:
                return validacaoCartaoCredito();

            case AKATUS_BOLETO:
                return true;

            case AKATUS_TEF:
                return validacaoTEF();

            default:
                return false;
        }
        
    } else {
        alert(MSG_MEIO_PAGAMENTO_INVALIDO);
        return false;
    }
}

function cpfValido(valor) {
    var soma;
    var resto;
    soma = 0;
    
    if (valor == "00000000000") return false;
     
    for (i=1; i<=9; i++) soma = soma + parseInt(valor.substring(i-1, i)) * (11 - i);
    resto = (soma * 10) % 11;
     
    if ((resto == 10) || (resto == 11))  resto = 0;
    if (resto != parseInt(valor.substring(9, 10)) ) return false;
     
    soma = 0;
    for (i = 1; i <= 10; i++) soma = soma + parseInt(valor.substring(i-1, i)) * (12 - i);
    resto = (soma * 10) % 11;
     
    if ((resto == 10) || (resto == 11))  resto = 0;
    if (resto != parseInt(valor.substring(10, 11) ) ) return false;
    
    return true;
}

function cartaoValido()
{
    var isValid = false;
	
	var cardType = $('input[name=bandeira_cartao]:checked').val();
	var cardNumber = $('input[name=cartao_numero]').val();
	
    var ccCheckRegExp = /[0-9]/;
	
    isValid = ccCheckRegExp.test(cardNumber);

    if (isValid) {
        var securityCode = $('input[name=cartao_codigo]');

		switch(cardType){
            
			case "cartao_master":
				if (securityCode.val().length != 3) {
					alert(MSG_CODIGO_SEG_INVALIDO_3);
                    securityCode.focus();
					return false;
				}
                break;
                
			 case "cartao_diners":
				if (securityCode.val().length != 3) {
					alert(MSG_CODIGO_SEG_INVALIDO_3);
                    securityCode.focus();
					return false;
				}
                break;                
                
			case "cartao_visa":
				if (securityCode.val().length != 3) {
					alert(MSG_CODIGO_SEG_INVALIDO_3);
                    securityCode.focus();
					return false;
				}
                break;
			
			case "cartao_amex":
				if (securityCode.val().length != 4) {
					alert(MSG_CODIGO_SEG_INVALIDO_4);
                    securityCode.focus();
					return false;
				}
                break;
			
			case "cartao_elo":
				if (securityCode.val().length != 3) {
					alert(MSG_CODIGO_SEG_INVALIDO_3);
                    securityCode.focus();
					return false;
				}
                break;                
			
			default:
                return false;
		}

    } else {
		alert(MSG_NUMERO_CARTAO_INVALIDO);
        $('input[name=cartao_numero]').focus();
		return false;
	}
	
	return true;
}

function detectarBandeira() {
    var visa    = 'cartao_visa';
    var master  = 'cartao_master';
    var elo     = 'cartao_elo';
    var amex    = 'cartao_amex';
    var diners = 'cartao_diners';    
    
    var img_visa    = $('img#' + visa);
    var img_master  = $('img#' + master);
    var img_elo     = $('img#' + elo);
    var img_amex    = $('img#' + amex);
    var img_diners = $('img#' + diners);

    var imagens = [img_visa, img_master, img_elo, img_amex, img_diners];

    var input_visa    = $('input[value=' + visa + ']');
    var input_master  = $('input[value=' + master + ']');
    var input_elo     = $('input[value=' + elo + ']');
    var input_amex    = $('input[value=' + amex + ']');
    var input_diners = $('input[value=' + diners + ']');

    var inputs = [input_visa, input_master, input_elo, input_amex, input_diners];
    
    if (($(this).val() === '')) {
        if ($('input[name=bandeira_cartao]').is(':checked')) {
            $('input[name=bandeira_cartao]:checked').prop('checked', false);
            $(imagens).each(function() {
                $(this).hide();
            });
        }
        return false;
    } 
    
    var first = $(this).val().charAt(0);
    var second = $(this).val().charAt(1);

    if (first === "4"){
        destacarBandeira(imagens, visa);
        selecionarInput(inputs, visa);        
    }
    else if (first === "5"){
        destacarBandeira(imagens, master);
        selecionarInput(inputs, master);
    }
    else if (first === "6"){
        destacarBandeira(imagens, elo);
        selecionarInput(inputs, elo);
    }
    else if ((first === "3") && ((second === "4") || (second === "7"))){
        destacarBandeira(imagens, amex);
        selecionarInput(inputs, amex);
    }
    else if ((first === "3") && ((second !== "4") && (second !== "7"))){
        destacarBandeira(imagens, diners);
        selecionarInput(inputs, diners);
    }
}

function destacarBandeira(imagens, imagem_destacada) {
    $(imagens).each(function() {
        imagem = $(this);
        (imagem.attr('id') === imagem_destacada) ? imagem.show() : imagem.hide();
    });    
}

function selecionarInput(inputs, input_selecionado) {
    $(inputs).each(function() {
        input = $(this);
        if (input.val() === input_selecionado) input.attr('checked', 'checked');
    });    
}

function mostrar_popup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'block';
}

function ocultar_popup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'none';
}