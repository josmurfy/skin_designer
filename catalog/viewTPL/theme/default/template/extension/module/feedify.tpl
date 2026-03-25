<!-- Feedify OpenCart Extension -->
<script id="feedify_webscript">
  var feedify = feedify || {};
  var s = document.createElement("script");
  window.feedify_options={fedify_url:"https://feedify.net/"};
  s.src=feedify_options.fedify_url+'getjs/feedbackembad-min-1.0.js';
  s.async=1;  
  window.addEventListener('load', function() {
    document.body.appendChild(s);
  }, true);
</script>

<?php if(isset($order_id)){ ?>
<!-- Feedify OpenCart Extension -->
<script id="feedify_webscript_ecommerce">
  var feedify_customer_data = {
    fname: '<?php echo $firstname; ?>',
    lname: '<?php echo $lastname; ?>',
    email: '<?php echo $email; ?>',
    order_id: '<?php echo $order_id; ?>'
  }
  window.feedify_options = feedify_customer_data; 
  window.feedify_options.fedify_url = 'https://feedify.net/'; 
  var s = document.createElement('script'); 
  s.src = feedify_options.fedify_url+'thirdparty/thirdpartycustomer?fname='+feedify_options.fname+'&lname='+feedify_options.lname+'&email='+feedify_options.email+'&order_id='+feedify_options.order_id;
  s.async = true;
  window.addEventListener('load', function() {
    document.body.appendChild(s);
  }, true);
</script>
<?php } ?>