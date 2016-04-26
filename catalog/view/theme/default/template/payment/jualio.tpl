<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" id="jualio-form">
  <input type="hidden" name="cmd" value="_cart" />
  <input type="hidden" name="upload" value="1" />
  <input type="hidden" name="business" value="<?php echo $business; ?>" />
  <?php $i = 1; ?>
  <?php foreach ($products as $product) { ?>
    <div class="jualio-item">
      <input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo $product['name']; ?>" />
      <input type="hidden" name="item_number_<?php echo $i; ?>" value="<?php echo $product['model']; ?>" />
      <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo $product['price']; ?>" />
      <input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo $product['quantity']; ?>" />
      <input type="hidden" name="weight_<?php echo $i; ?>" value="<?php echo $product['weight']; ?>" />
      <input type="hidden" name="image_<?php echo $i; ?>" value="<?php echo $product['image']; ?>" />
      <input type="hidden" name="description_<?php echo $i; ?>" value="<?php echo $product['description']; ?>" />
      <?php $j = 0; ?>
      <?php foreach ($product['option'] as $option) { ?>
        <input type="hidden" name="on<?php echo $j; ?>_<?php echo $i; ?>" value="<?php echo $option['name']; ?>" />
        <input type="hidden" name="os<?php echo $j; ?>_<?php echo $i; ?>" value="<?php echo $option['value']; ?>" />
        <?php $j++; ?>
      <?php } ?>
      <?php $i++; ?>
    </div>
  <?php } ?>
  <?php if ($discount_amount_cart) { ?>
    <input type="hidden" name="discount_amount_cart" value="<?php echo $discount_amount_cart; ?>" />
  <?php } ?>
  <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>" />
  <input type="hidden" name="first_name" value="<?php echo $first_name; ?>" />
  <input type="hidden" name="last_name" value="<?php echo $last_name; ?>" />
  <input type="hidden" name="address1" value="<?php echo $address1; ?>" />
  <input type="hidden" name="address2" value="<?php echo $address2; ?>" />
  <input type="hidden" name="city" value="<?php echo $city; ?>" />
  <input type="hidden" name="zip" value="<?php echo $zip; ?>" />
  <input type="hidden" name="telephone" value="<?php echo $telephone; ?>" />
  <input type="hidden" name="country" value="<?php echo $country; ?>" />
  <input type="hidden" name="address_override" value="0" />
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="invoice" value="<?php echo $invoice; ?>" />
  <input type="hidden" name="lc" value="<?php echo $lc; ?>" />
  <input type="hidden" name="rm" value="2" />
  <input type="hidden" name="no_note" value="1" />
  <input type="hidden" name="charset" value="utf-8" />
  <input type="hidden" name="return" value="<?php echo $return; ?>" />
  <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
  <input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>" />
  <input type="hidden" name="paymentaction" value="<?php echo $paymentaction; ?>" />
  <input type="hidden" name="custom" value="<?php echo $custom; ?>" />
  <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />

  <input type="hidden" name="jualio_customer_key" value="<?php echo $jualio_customer_key; ?>" />
  <input type="hidden" name="jualio_client_id" value="<?php echo $jualio_client_id; ?>" />
  <input type="hidden" name="jualio_url" value="<?php echo $jualio_url; ?>" />
  <input type="hidden" name="jualio_payment_channel_type" value="<?php echo $jualio_payment_channel_type; ?>" />
  <input type="hidden" name="jualio_payment_channel_direct" value="<?php echo $jualio_payment_channel_direct; ?>" />


  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button"/>
    </div>
  </div>
</form>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    console.log('ready i am');
    var jualioForm = $('#jualio-form');
    var jualioPaymentRequest = {
      object : 'payment',
      callback_url : jualioForm.find('[name="notify_url"]').val()+'&orderid='+jualioForm.find('[name="orderid"]').val(),
      customer_key : jualioForm.find('[name="jualio_customer_key"]').val(),
      invoice_no : jualioForm.find('[name="invoice"]').val(),
      carts : [],
      buyer_data : {
        'name' : jualioForm.find('[name="first_name"]').val() + ' ' + jualioForm.find('[name="last_name"]').val() ,
        'email' : jualioForm.find('[name="email"]').val(),
        'mobile_no' : jualioForm.find('[name="telephone"]').val(),
        'address' : jualioForm.find('[name="address1"]').val() + ' ' + jualioForm.find('[name="address2"]').val() + ' ' + jualioForm.find('[name="city"]').val() ,
      },
      payment_channel : {
        type:  jualioForm.find('[name="jualio_payment_channel_type"]').val() ,
        direct: (jualioForm.find('[name="jualio_payment_channel_direct"]').val() === 'TRUE') 
      }
    }
    jualioForm.find('.jualio-item').each(function(index, el) {
      var item = {
        name : $(this).find('[name^="item_name"]').val(),
        amount : parseInt( $(this).find('[name^="amount"]').val()),
        quantity : parseInt( $(this).find('[name^="quantity"]').val()),
        category : 'plain',
        description : $(this).find('[name^="description"]').val(),
        image : $(this).find('[name^="image"]').val()
      }
      jualioPaymentRequest.carts.push(item);
    });

    jualioForm.on('submit', function(event) {
      event.preventDefault();
      console.log('submit : ' + jualioForm.find('[name="jualio_client_id"]').val());
      console.log('request', JSON.stringify(jualioPaymentRequest));

      $.ajax({
        url: jualioForm.find('[name="jualio_url"]').val(),
        headers: {'Authorization': 'Basic ' + jualioForm.find('[name="jualio_client_id"]').val()},
        data: JSON.stringify(jualioPaymentRequest),
        type: 'POST',
        processData: false,
        contentType: 'application/json'
      })
      .done(function(jualioPaymentResponse) {
        console.log("success", jualioPaymentResponse);
        window.location.href = jualioPaymentResponse.data.payment_url;
      })
      .fail(function(jualioPaymentResponse) {
        console.log("error", jualioPaymentResponse);
      });

    });

    console.log('request', jualioPaymentRequest);
  });
</script>