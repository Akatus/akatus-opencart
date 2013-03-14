<h2>Endereço de Entrega</h2>

<?php if (! empty($addresses)) { ?>
<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
<label for="shipping-address-existing">Quero utilizar um endereço já cadastrado</label>
<div id="shipping-existing">
  <select name="shipping_address_id" style="width: 100%; margin-bottom: 15px;" size="5">
    <?php foreach ($addresses as $address) { ?>
    <?php if ($address['address_id'] == $address_id) { ?>
    <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
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

    <span class="required">*</span> Sobrenome:
    <input type="text" name="shipping_lastname" value="" class="akatus-field" />

    <span class="required">*</span> Endereço:
    <input type="text" name="shipping_address_1" value="" class="akatus-field" />

    <span class="required">*</span> Bairro:<br />
    <input type="text" name="shipping_address_2" value="" class="akatus-field" />    
    
    <span class="required">*</span> Cidade:
    <input type="text" name="shipping_city" value="" class="akatus-field" />

    <span id="shipping-postcode-required" class="required">*</span> CEP:
    <input type="text" name="shipping_postcode" value="" class="akatus-field" />

    <span class="required">*</span> País:
    <select name="shipping_country_id" class="akatus-field">
        <option value="">--- Selecione ---</option>
        <?php foreach ($countries as $country) { ?>
        <?php if ($country['country_id'] == $country_id) { ?>
        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
        <?php } ?>
        <?php } ?>
    </select>

    <span class="required">*</span> Estado:
    <select name="shipping_zone_id" class="akatus-field">--- Selecione ---</select>
    <br />
    <br />
</div>

<script>
    $(function() {
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
</script>