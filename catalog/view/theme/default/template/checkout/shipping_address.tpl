<h2>Endereço de Entrega</h2>

<?php if (! empty($addresses)) { ?>
<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
<label for="shipping-address-existing">Quero utilizar um endereço já cadastrado</label>
<div id="shipping-existing">
  <select name="shipping_address_id" style="width: 100%; margin-bottom: 15px;" size="3">
    <?php foreach ($addresses as $address) { ?>
    <?php if ($address['address_id'] == $address_id) { ?>
    <option value="<?php echo $address['address_id']; ?>" selected="selected" data-postcode="<?php echo $address['postcode'] ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $address['address_id']; ?>" data-postcode="<?php echo $address['postcode'] ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
</div>
<p>
  <input type="radio" name="shipping_address" value="new" id="shipping-address-new" />
  <label for="shipping-address-new">Quero utilizar um novo endereço</label>
</p>
<?php } ?>

<div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
    <span class="required">*</span> Nome:
    <input type="text" name="shipping_firstname" value="" class="akatus-field" />
    <?php if (isset($error['shipping_firstname'])) echo "<span class='error'>" . $error['shipping_firstname'] . "</span>" ?>

    <span class="required">*</span> Sobrenome:
    <input type="text" name="shipping_lastname" value="" class="akatus-field" />
    <?php if (isset($error['shipping_lastname'])) echo "<span class='error'>" . $error['shipping_lastname'] . "</span>" ?>

    <span id="shipping-postcode-required" class="required">*</span> CEP:
    <input type="text" id="shipping_postcode" name="shipping_postcode" value="" class="akatus-field" />
    <?php if (isset($error['shipping_postcode'])) echo "<span class='error'>" . $error['shipping_postcode'] . "</span>" ?>

    <div id="shipping_address_container">
        <span class="required">*</span> Endereço:
        <input type="text" name="shipping_address_1" id="shipping_address_1" value="" class="akatus-field" />
        <?php if (isset($error['shipping_address_1'])) echo "<span class='error'>" . $error['shipping_address_1'] . "</span>" ?>

        <span class="required">*</span> Bairro:<br />
        <input type="text" name="shipping_address_2" id="shipping_address_2" value="" class="akatus-field" />
        <?php if (isset($error['shipping_address_2'])) echo "<span class='error'>" . $error['shipping_address_2'] . "</span>" ?>
        
        <span class="required">*</span> Cidade:
        <input type="text" name="shipping_city" id="shipping_city" value="" class="akatus-field" />
        <?php if (isset($error['shipping_city'])) echo "<span class='error'>" . $error['shipping_city'] . "</span>" ?>
        
        <span class="required">*</span> Estado:
        <select name="shipping_zone_id" id="shipping_zone_id" class="akatus-field">--- Selecione ---</select>
        <?php if (isset($error['shipping_zone'])) echo "<span class='error'>" . $error['shipping_zone'] . "</span>" ?>

        <span class="required">*</span> País:
        <select name="shipping_country_id" id="shipping_country_id" class="akatus-field">
            <option value="">--- Selecione ---</option>
            <?php foreach ($countries as $country) { ?>
            <?php if ($country['country_id'] == $country_id) { ?>
            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
            <?php } ?>
            <?php } ?>
        </select>
        <?php if (isset($error['shipping_country'])) echo "<span class='error'>" . $error['shipping_country'] . "</span>" ?>
    </div>
    <br />
    <br />
</div>

<script>
    $(function() {
        $('#shipping_postcode').mask('00000-000');

        $('input[name=shipping_address]').change(function() {

            switch ($(this).val()) {
                
               case 'existing':
                  $("#shipping-new").hide();
                  break;
                  
               case 'new':
                  $("#shipping-new").show();
                  break;

               default:
                   break;
            }
        });
    });

    $('#shipping_postcode').blur(function(){
        var cep = $(this).val();
        $('#shipping_address_container').slideDown();
        $('#shipping_address_1').focus();

        $.ajax({
            url: "https://lvws0001.lojablindada.com/endereco/?format=json&cep="+cep,
            jsonp: "callback",
            dataType: "jsonp",
            data: {
                format: "json"
            },
            success: function( response ) {
                $('#shipping_address_1').val(response.endereco);
                $('#shipping_address_2').val(response.bairro);
                $('#shipping_city').val(response.cidade);

                $('#shipping_zone_id option').each(function() {
                    var estado = $(this).html();
                    var estado_slug = slug_estado(estado);
                    
                    if(estado_slug.toUpperCase() == response.estado){
                        $(this).prop("selected", true); 
                    }

                    if(estado_slug.toUpperCase() == response.estado){
                        $(this).prop("selected", true); 
                    }

                });
            }
        });
    })
</script>

<style type="text/css">
    #shipping_address_container { display: none; }
</style>