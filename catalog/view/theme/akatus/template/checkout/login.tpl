<?php echo $header; ?>

<?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>

<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <div class="login-content">
    <div class="left">
      <h2><?php echo $text_new_customer; ?></h2>
      <div class="content">
        <p><b><?php echo $text_register; ?></b></p>
        <p><?php echo $text_register_account; ?></p>
        <a href="index.php?route=checkout/checkout&register=1" class="button"><?php echo $button_continue; ?></a></div>
    </div>
    <div class="right">
      <h2><?php echo $text_returning_customer; ?></h2>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="content">
          <p><?php echo $text_i_am_returning_customer; ?></p>
          <b><?php echo $entry_email; ?></b><br />
          <input type="text" name="email" value="<?php echo $email; ?>" />
          <br />
          <br />
          <b><?php echo $entry_password; ?></b><br />
          <input type="password" name="password" value="<?php echo $password; ?>" />
          <br />
          <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a><br />
          <br />
          <input type="button" id="button-login" value="<?php echo $button_login; ?>" class="button" />
        </div>
      </form>
    </div>
  </div>
  <?php echo $content_bottom; ?></div>

<script type="text/javascript">

    $(function() {
        $('#button-login').live('click', function() {
            $.ajax({
                url: 'index.php?route=checkout/login/validate',
                type: 'post',
                data: $('form').serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#button-login').attr('disabled', true);
                    $('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
                },	
                complete: function() {
                    $('#button-login').attr('disabled', false);
                    $('.wait').remove();
                },				
                success: function(json) {
                    $('.warning, .error').remove();

                    if (json['redirect']) {
                        location = json['redirect'];
                    } else if (json['error']) {
                        $('.login-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
                        $('.warning').fadeIn('slow');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });	
        });
    });

</script> 
<?php echo $footer; ?>