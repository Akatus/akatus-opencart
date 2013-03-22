<?php if ($shipping_required) { ?>

    <h2>Métodos de Entrega</h2>

    <table class="radio">

        <?php if ($shipping_methods) { ?>
            <?php foreach ($shipping_methods as $shipping_method) { ?>
                <?php if (!$shipping_method['error']) { ?>
                    <?php foreach ($shipping_method['quote'] as $quote) { ?>
                        <tr>
                            <td><?php if ($quote['code'] == $code) { ?>
                                <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
                                <?php } ?></td>
                            <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
                            <td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>

    </table>
    <br />

<?php } ?>