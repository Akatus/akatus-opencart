function is_int(value){
  if((parseInt(value)) && !isNaN(value)){
      return true;
  } else {
      return false;
  }
}

function validarDDD()
{
	if(document.getElementById('cartao_telefone_ddd').value.length!=2)
	{
		alert('Por favor, preencha corretamente o campo DDD. Ele deve ter 2 dígitos numéricos.');
		return false;
	}
	
	if(!is_int(document.getElementById('cartao_telefone_ddd').value))
	{
		alert('Por favor, preencha corretamente o campo DDD. Ele deve ter 2 dígitos numéricos.');
		return false;
	}
	
	return true;
}



function validarTelefone()
{	
	if((document.getElementById('cartao_telefone').value.length)<8)
	{
		alert('Por favor, preencha corretamente o campo Telefone. Ele deve ter 8 a 9 dígitos numéricos.');
		return false;
	}
		
	if(!is_int(document.getElementById('cartao_telefone').value))
	{
		alert('Por favor, preencha corretamente o campo Telefone. Ele deve ter 8 a 9 dígitos numéricos.');
		return false;
	}

	
	return true;
}



function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}



function validarCartao()
{
    var isValid = false;
	
	var cardType = getCheckedValue(document.forms['pagamento'].elements['bandeira_cartao']);
	var cardNumber = document.getElementById('cartao_numero').value;
	
    var ccCheckRegExp = /[^\d ]/;
	
    isValid = !ccCheckRegExp.test(cardNumber);
	
    if (isValid){
        var cardNumbersOnly = cardNumber.replace(/ /g,"");
        var cardNumberLength = cardNumbersOnly.length;
        var lengthIsValid = false;
        var prefixIsValid = false;
        var prefixRegExp;

		switch(cardType){
			case "cartao_master":
				lengthIsValid = (cardNumberLength == 16);
				
				if(!is_int(document.getElementById('cartao_codigo').value))
				{
					alert("Erro: O código de Segurança do cartão deve ser numérico!");
					return false;
					
				}
				
				if(document.getElementById('cartao_codigo').value.length!=3)
				{
					alert("Erro: O código de Segurança do cartão deve ter 3 dígitos!");
					return false;
					
				}
				
				prefixRegExp = /^5[1-5]/;
			break;
			
			
			 case "cartao_diners":
				lengthIsValid = (cardNumberLength == 14);
				
				if(!is_int(document.getElementById('cartao_codigo').value))
				{
					alert("Erro: O código de Segurança do cartão deve ser numérico!");
					return false;
					
				}
				
				if(document.getElementById('cartao_codigo').value.length!=3)
				{
					alert("Atenção: O código de Segurança do cartão deve ter 3 dígitos!");
					return false;
					
				}
				
				prefixRegExp = /^3/;
			break;
			
			case "cartao_visa":
				lengthIsValid = (cardNumberLength == 16 || cardNumberLength == 13);
				
				if(!is_int(document.getElementById('cartao_codigo').value))
				{
					alert("Erro O código de Segurança do cartão deve ser numérico!");
					return false;
					
				}
				
				if(document.getElementById('cartao_codigo').value.length!=3)
				{
					alert("Atenção: O código de Segurança do cartão deve ter 3 dígitos!");
					return false;
					
				}
				prefixRegExp = /^4/;
			break;
			
			case "cartao_amex":
				lengthIsValid = (cardNumberLength == 15);
				if(document.getElementById('cartao_codigo').value.length!=4)
				{
					alert("Atenção: O código de Segurança do cartão deve ter 4 dígitos!");
					return false;
					
				}
				prefixRegExp = /^3/;
			break;
			
			case "cartao_elo":
				lengthIsValid = (cardNumberLength == 16);
				
				if(document.getElementById('cartao_codigo').value.length!=3)
				{
					alert("Atenção: O código de Segurança do cartão deve ter 3 dígitos!");
					return false;
					
				}
				prefixRegExp = /^6/;
			break;
			
			default:
				prefixRegExp = /^$/;
				alert("Por favor, selecione a bandeira do seu cartão de crédito");
				
			break;
		}
	
		prefixIsValid = prefixRegExp.test(cardNumbersOnly);
		isValid = prefixIsValid && lengthIsValid;
    }
	else
	{
		alert("Por favor, informe corretamente o número de seu cartão de crédito");
		return false;
		
	}
	
    if (isValid)
	{
        var numberProduct;
        var numberProductDigitIndex;
        var checkSumTotal = 0;
        for (digitCounter = cardNumberLength - 1; digitCounter >= 0; digitCounter--){
            checkSumTotal += parseInt (cardNumbersOnly.charAt(digitCounter));
            digitCounter--;
            numberProduct = String((cardNumbersOnly.charAt(digitCounter) * 2));
            for (var productDigitCounter = 0; productDigitCounter < numberProduct.length; productDigitCounter++){
                checkSumTotal += parseInt(numberProduct.charAt(productDigitCounter));
            }
        }
        isValid = (checkSumTotal % 10 == 0);
    }
	if(isValid==false)
	{
		alert("Cartão de Crédito Inválido! Por favor, confira os números para prosseguir.");
		return false;
		
	}
	return true;
}


function verificarCPF(c)
{
		if (c.length != 11 || c == "00000000000" || c == "11111111111" || c == "22222222222" || c == "33333333333" || c == "44444444444" || c == "55555555555" || c == "66666666666" || c == "77777777777" || c == "88888888888" || c == "99999999999")
		{
			alert('CPF Inválido!');
			return false;
		}
		
		var i;
		s = c;
		var c = s.substr(0,9);
		var dv = s.substr(9,2);
		var d1 = 0;
		var v = false;

		for (i = 0; i < 9; i++)
		{
			d1 += c.charAt(i)*(10-i);
		}
		if (d1 == 0)
		{
			alert("CPF Inválido!");
			v = true;
			
			return false;
		}
		d1 = 11 - (d1 % 11);
		
		if (d1 > 9) d1 = 0;
		
		if (dv.charAt(0) != d1)
		{
			alert("CPF Inválido!");
			v = true;
			return false;
		}
	 
		d1 *= 2;
		for (i = 0; i < 9; i++)
		{
			d1 += c.charAt(i)*(11-i);
		}
		
		d1 = 11 - (d1 % 11);
		
		if (d1 > 9) d1 = 0;
		
		if (dv.charAt(1) != d1)
		{
			alert("CPF Inválido");
			v = true;
			
			return false;
		}
		if (!v) 
		{
			return true;
		}
		
		return false;
	}
	
	function define_cartao(cartao)
	{
		
		
		//muda as imagens
		document.getElementById('cartao_master').src="/image/akatus/cartao_master2.gif";
		document.getElementById('cartao_visa').src="/image/akatus/cartao_visa2.gif";
		document.getElementById('cartao_elo').src="/image/akatus/cartao_elo2.gif";
		document.getElementById('cartao_amex').src="/image/akatus/cartao_amex2.gif";
		document.getElementById('cartao_diners').src="/image/akatus/cartao_diners2.gif";
		
		document.getElementById(cartao).src="/image/akatus/"+cartao+".gif";
		
		document.getElementById('dados_titular_cartao').style.display="block";
		
	}
	
	function pagar()
	{
		if(document.getElementById('cartao_titular').value=="")
		{
			alert('Por favor, informe o nome do titular do cartão');
			return false;
		}
		
		if(validarCartao()==false)
			return false;
					
		var cartao_mes = document.getElementById("cartao_mes");
		cartao_mes = cartao_mes.options[cartao_mes.selectedIndex].value;
		
		if(cartao_mes==-1)
		{
			
			alert('Por favor, informe o mês da validade do cartão');
			return false;
			
		}
		
		var cartao_ano = document.getElementById("cartao_ano");
		cartao_ano = cartao_ano.options[cartao_ano.selectedIndex].value;
		
		if(cartao_ano==-1)
		{
			
			alert('Por favor, informe o ano da validade do cartão');
			return false;
			
		}

		
		if(document.getElementById('cartao_cpf').value=="")
		{
			alert('Por favor, informe o CPF do Titular do Cartão');
			return false;
		}
		
		if(!validarDDD())
			return false;
					
		if(!validarTelefone())
			return false;
					
		if(verificarCPF(document.getElementById('cartao_cpf').value)==false)
		{
			return false;
		}
		
		document.getElementById('div_botao_enviar').style.display="none";
		document.getElementById('carregando').style.display="block";
		
		return true;
		
	}
	function mostrar_popup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'block';
	window.scrollTo(0, 0);
  }
  
  function ocultar_popup() {
    var popup = document.getElementById('popup');
    popup.style.display = 'none';
  }
  
  
  