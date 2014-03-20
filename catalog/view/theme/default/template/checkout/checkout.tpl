<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>

<link href="akatus/estilos.css" rel="stylesheet" type="text/css" />

<div id="content">
    <?php echo $content_top; ?>
    
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    
    <h1><?php echo $heading_title; ?></h1>
    
    <div class="checkout">
        
        <?php if (isset($error_warning)) { ?>
            <div class="warning">
                <?php echo $error_warning; ?>
                <img src="catalog/view/theme/default/image/close.png" alt="" class="close" />
            </div>
        <?php } ?>
        
        <form id="one-step-checkout" action="index.php?route=checkout/checkout/validate" method="post">

            <div id="left-column">
                <div id="payment-address">
                <?php if (!$logged) { ?>      
                    <div><?php include 'register.tpl' ?></div>
                <?php } else { ?>
                    <div><?php include 'payment_address.tpl' ?></div>
                <?php } ?>  
                </div>
                
                <div id="shipping-address">
                    <div><?php if ($shipping_required) include 'shipping_address.tpl' ?></div>
                </div>                
            </div>
            
            <div id="center-column">
                <div id="shipping-method">
                    <?php include 'shipping_method.tpl' ?>
                </div>

                <div id="payment-method">
                    <div><?php include 'payment_method.tpl' ?></div>
                </div>
            </div>

            <div id="right-column">
                <div id="confirm">
                    <div id="total"><?php include 'confirm.tpl' ?></div>

                    <div class="buttons">
                        <div class="right">
                            <input type="submit" value="Finalizar Compra" class="button" />
                        </div>
                    </div>
                </div>                
            </div>
        </form>
    </div>
</div>

<input type="hidden" id="logged" name="logged" value="<?php if ($logged) echo 'true'; ?>" />
<input type="hidden" id="shipping_required" name="shipping_required" value="<?php if ($shipping_required) echo 'true'; ?>" />

<script type="text/javascript">
    <?php
        $this->load->model('setting/setting');
        $current_settings = $this->model_setting_setting->getSetting('akatus');
        //$is_sandbox = $current_settings['akatus_tipo_conta'] != 'PRODUCAO';
        $public_token = isset($current_settings['akatus_public_token']) ? $current_settings['akatus_public_token'] : '';
    ?>

    $.getScript("https://static.akatus.com/js/akatus.min.js",function() {
        var formulario = $('#one-step-checkout');
        var config = {
            <?php
                if (isset($current_settings['akatus_tipo_conta'])){
                    if($current_settings['akatus_tipo_conta'] != 'PRODUCAO'){
                        echo "sandbox: true,";
                    }
                }else{
                    echo "sandbox: true,";
                }
            ?>
            publicToken: '<?php echo $public_token; ?>'
        };
        Akatus.init(formulario, config);
    });

</script>

<script type="text/javascript" src="catalog/view/javascript/akatus.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery.mask.min.js"></script>

<?php echo $content_bottom; ?>
<?php echo $footer; ?>
