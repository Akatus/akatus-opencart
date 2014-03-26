<?php

class ControllerInformationAkatus extends Controller 
{
   private $error = array();
      
     public function index() {
      $this->language->load('information/akatus'); 
		
         $this->data['breadcrumbs'] = array();

         $this->data['breadcrumbs'][] = array(
           'text'      => $this->language->get('text_home'),
         'href'      => $this->url->link('common/home'),           
           'separator' => false
         );

		$tipo=$_REQUEST['tipo'];
	
	   if($tipo==1)
	   {
		   $this->document->setTitle('Pedido Concluído - Pagamento em Análise'); 
		   $this->data['heading_title'] = 'Pagamento em Análise'; 
		   $this->data['conteudo_centro'] = 'Obrigado por seu pedido! Seu pagamento encontra-se em análise pela operadora do seu cartão e assim que aprovado, você receberá um e-mail informando.';
	   }
	   else 
	   if($tipo==2)
	   {
		   $this->document->setTitle('Pagamento não autorizado'); 
		   $this->data['heading_title'] = 'Pagamento não autorizado'; 
		   $this->data['conteudo_centro'] = 'Seu pagamento não foi autorizado pela operadora do cartão. Você pode ter digitado algum dado errado, ou a operação ultrapassa o limite atual disponível no seu cartão de crédito. Por favor, efetue um novo pedido e verifique se os seus dados estão corretos. Caso seja necessário, você também poderá escolher uma outra forma de pagamento.';

	   }
	   else if($tipo==3)
	   {
		   #pagamento aprovado
		   
		   $this->document->setTitle('Pedido Concluído - Pagamento Aprovado'); 
		   $this->data['heading_title'] = 'Pagamento Aprovado'; 
		   $this->data['conteudo_centro'] = 'Obrigado por seu pedido! Seu pagamento foi Aprovado com sucesso pela operadora do seu cartão de crédito e em breve o envio começará a ser processado. Você receberá por e-mail novas informações de acordo com o andamento do seu pedido.';
		   
	   }
        else if($tipo==4)
		{
			#erro
			
		   $this->document->setTitle('Erro no pagamento'); 
		   $this->data['heading_title'] = "Não foi possível concluir o pedido."; 
		   $this->data['conteudo_centro'] = 'Se o erro persistir, entre em contato com o administrador da loja.<BR>';

		}
		else if($tipo==5)
		{
		   $this->data['heading_title'] = "Obrigado por seu pedido!"; 
		   $this->data['conteudo_centro'] = 'Seu pedido foi enviado com sucesso. Contudo, é necessário que seja efetuado o pagamento do boleto bancário para que ele possa ser processado. Para imprimir seu boleto, clique no botão abaixo:<BR><BR><a href="https://www.akatus.com/boleto/'.urldecode($_REQUEST['url_boleto']).'.html" target="_blank"><img src="image/botao_imprimir_boleto.png" /></a><BR>';
			
		}
		else if($tipo==6)
		{
			#Pagamento concluído com TEF / Débito
			$this->document->setTitle('Pedido Concluído - Pagamento Aprovado'); 
		   $this->data['heading_title'] = 'Pagamento Aprovado'; 
		   $this->data['conteudo_centro'] = 'Obrigado por seu pedido! Seu pagamento foi Aprovado com sucesso pelo seu banco e em breve o envio começará a ser processado. Você receberá por e-mail novas informações de acordo com o andamento do seu pedido.';
		}
		else
		{
			exit;
		}

      if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/akatus.tpl')) 
	  { 
         $this->template = $this->config->get('config_template') . '/template/information/akatus.tpl';
      } else {
         $this->template = 'default/template/information/akatus.tpl'; 
      }
      
      $this->children = array(
         'common/column_left',
         'common/column_right',
         'common/content_top',
         'common/content_bottom',
         'common/footer',
         'common/header'
      );
            
      $this->response->setOutput($this->render());      
     }
}
?>
