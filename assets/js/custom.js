
  var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
  jQuery(function($) {
jQuery("#countryCode").change(function() {
      var countryid = jQuery(this).children(":selected").val();
      if(countryid != '') {
        var data = {
          action: 'get_price_by_ajax',
          country: countryid,
          'security': '<?php echo wp_create_nonce("load_price"); ?>'
        }

        $.post(ajaxurl, data, function(response) {
          $('.load-price').html(response);
        });
      }
    });
  });

