<?php if ($payment_methods) { ?>

<h2>Forma de Pagamento</h2>

<table class="radio">
    <?php foreach ($payment_methods as $payment_method) { ?>
        <tr>
            <td><?php if ($payment_method['code'] == $code) { ?>
                <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
                <?php } ?></td>
            <td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label></td>
        </tr>

        <?php if ($payment_method['code'] == 'akatus') { ?>
            <tr class="cartoes">
                <td></td>
                <td><?php include 'akatus.tpl' ?></td>
            </tr>
        <?php } ?>

        <?php if ($payment_method['code'] == 'akatust') { ?>
            <tr class="tef">
                <td></td>
                <td><?php include 'akatust.tpl' ?></td>
            </tr>    
        <?php } ?>    

    <?php } ?>
</table>
<br />
<?php } ?>
