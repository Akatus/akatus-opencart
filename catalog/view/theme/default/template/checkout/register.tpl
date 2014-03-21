<div class="left">
    <h2>Dados Pessoais</h2>

    <span class="required">*</span> Nome:<br />
    <input type="text" name="firstname" value="<?php if (isset($firstname)) echo $firstname ?>" class="akatus-field" />
    <?php if (isset($error['firstname'])) echo "<span class='error'>" . $error['firstname'] . "</span>" ?>

    <br />
    
    <span class="required">*</span> Sobrenome:<br />
    <input type="text" name="lastname" value="<?php if (isset($lastname)) echo $lastname ?>" class="akatus-field" />
    <?php if (isset($error['lastname'])) echo "<span class='error'>" . $error['lastname'] . "</span>" ?>
    
    <br />
    
    <span class="required">*</span> E-Mail:<br />
    <input type="text" name="email" value="<?php if (isset($email)) echo $email ?>" class="akatus-field" />
    <?php if (isset($error['email'])) echo "<span class='error'>" . $error['email'] . "</span>" ?>

    <br />
    
    <span class="required">*</span> Telefone:<br />
    <input type="text" name="telephone" value="<?php if (isset($telephone)) echo $telephone ?>" class="akatus-field" />
    <?php if (isset($error['telephone'])) echo "<span class='error'>" . $error['telephone'] . "</span>" ?>

    <br /><br />
    
    <span class="required">*</span> Senha:<br />
    <input type="password" name="password" value="<?php if (isset($password)) echo $password ?>" class="akatus-field" />
    <?php if (isset($error['password'])) echo "<span class='error'>" . $error['password'] . "</span>" ?>    

    <br />
    
    <span class="required">*</span> Confirmação da Senha:<br />
    <input type="password" name="confirm" value="<?php if (isset($confirm)) echo $confirm ?>" class="akatus-field" />
    <?php if (isset($error['confirm'])) echo "<span class='error'>" . $error['confirm'] . "</span>" ?>
    
    <input type="hidden" name="fax" value="">
    <input type="hidden" name="company" value="">
    <input type="hidden" name="company_id" value="">
    <input type="hidden" name="tax_id" value="">
    
    <br />
    <br />
</div>
<div class="left">
    <h2>Endereço de Cobrança</h2>
        <span id="payment-postcode-required" class="required">*</span> CEP:<br />
        <input type="text" name="postcode" id="postcode" value="<?php if (isset($postcode)) echo $postcode ?>" class="akatus-field" />
        <?php if (isset($error['postcode'])) echo "<span class='error'>" . $error['postcode'] . "</span>" ?>
        <br />
        
    <div id="address_container">
        <span class="required">*</span> Endereço:<br />
        <input type="text" name="address_1" id="address_1" value="<?php if (isset($address_1)) echo $address_1 ?>" class="akatus-field" />
        <?php if (isset($error['address_1'])) echo "<span class='error'>" . $error['address_1'] . "</span>" ?>
        <br />
        
        <span class="required">*</span> Bairro:<br />
        <input type="text" name="address_2" id="address_2" value="<?php if (isset($address_2)) echo $address_2 ?>" class="akatus-field" />
        <?php if (isset($error['address_2'])) echo "<span class='error'>" . $error['address_2'] . "</span>" ?>
        <br />    
        
        <span class="required">*</span> Cidade:<br />
        <input type="text" name="city" id="city" value="<?php if (isset($city)) echo $city ?>" class="akatus-field" />
        <?php if (isset($error['city'])) echo "<span class='error'>" . $error['city'] . "</span>" ?>
        <br />
        
        <span class="required">*</span> Estado:<br />
        <select name="zone_id" id="zone_id" class="akatus-field"></select>
        <?php if (isset($error['zone'])) echo "<span class='error'>" . $error['zone'] . "</span>" ?>
        
        <span class="required">*</span> País:<br />
        <select name="country_id" class="akatus-field">
            <?php foreach ($countries as $country) { ?>
            <?php if ($country['country_id'] == $country_id) { ?>
            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
            <?php } ?>
            <?php } ?>
        </select>
        <?php if (isset($error['country'])) echo "<span class='error'>" . $error['country'] . "</span>" ?>
    </div>
    <br />
    <br />
    <br />
</div>
<div>
    <?php if ($shipping_required) { ?>
        <input type="checkbox" name="shipping_address" value="new" id="shipping" />
        <label for="shipping">Entregar em outro endereço.</label>
    <?php } ?>
    <br />
    <br />    
</div>

<script>
    $(function() {
        $('#postcode').mask('00000-000');

        $('input[name=payment_address]').change(function() {

            switch ($(this).val()) {
                
               case 'existing':
                  $("#payment-new").hide();
                  break;
                  
               case 'new':
                  $("#payment-new").show();
                  break;

               default:
                   break;
            }
        });
    });

    $('#postcode').blur(function(){
        var cep = $(this).val();
        $('#address_container').slideDown();
        $('#address_1').focus();

        $.ajax({
            url: "https://lvws0001.lojablindada.com/endereco/?format=json&cep="+cep,
            jsonp: "callback",
            dataType: "jsonp",
            data: {
                format: "json"
            },
            success: function( response ) {

                $('#address_1').val(response.endereco);
                $('#address_2').val(response.bairro);
                $('#city').val(response.cidade);

                $('#zone_id option').each(function() {
                    var estado = $(this).html();
                    var estado_slug = slug_estado(estado);
                    
                    if(estado_slug.toUpperCase() == response.estado){
                        $(this).prop("selected", true); 
                    }
                });
            }
        });
    })
</script>

<style type="text/css">
    #address_container { display: none; }
</style>