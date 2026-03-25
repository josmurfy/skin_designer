<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
</head>
<body>
<div class="container">
  
  <div style="page-break-after: always;">
    <div class="col-sm-12">
      <div class="text-center">
        <?php if(!empty($store_logo)) { ?>
        <img src="<?php echo $storelogo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" style="margin: 0 auto 20px; padding-top:10px"/> 
        <?php } ?>
      </div>
      <div class="text-center" style="padding-top:10px">
        <address>
          <?php if(!empty($store_name)) { ?>
          <strong>* <?php echo $config_name; ?> *</strong>
          <?php } ?>
          <br >
          <?php if(!empty($store_address)) { ?>
          <?php echo $config_address; ?>
          <?php } ?>
          <br />
          <?php if(!empty($store_telephone)) { ?>
          <?php echo $config_telephone; ?>
          <?php } ?>
        </address>
      </div>
      <div class="pull-left">
        <?php if(!empty($store_order_date)) { ?>
        <b><?php echo $text_date_added; ?> :</b> <?php echo $date_added; ?><br />
        <?php } ?>
        
        <?php if(!empty($invoice_number)) { ?>
        <h6><?php echo $text_invoice; ?> : #<?php echo $order_id; ?></h6>
        <?php } ?>
      </div>
      <div class="text-right">
        <?php if(!empty($store_order_time)) { ?>
        <b>Time :</b> <?php echo $time; ?><br />
        <?php } ?>
      </div>
      <br />
      <br />
      <?php if(!empty($cashier_name)) { ?>
      <?php if($usernames) { ?><p><?php echo $text_cashier; ?> : <?php echo $usernames; ?></p><?php } ?>
      <?php } ?>
      <table class="table">
        <thead>
          <tr>
            <td>
              <div class="text-left"><b><?php echo $text_code; ?></b></div>
              <div class="text-center"><b><?php echo $text_qty; ?></b></div>
            </td>
            <td class="text-center">
              <b><?php echo $text_description; ?></b><br />
              <b><?php echo $text_price; ?></b>
            </td>
            <td class="text-right">
              <b><?php echo $text_due_date; ?></b>
            </td>
          </tr>
        </thead>
        <tbody>
          <?php foreach($products as $product) { ?>
          <tr class="des">
            <td>
              <div class="text-left"><?php echo $product['model']; ?></div>
              <div class="text-center"><?php echo $product['quantity']; ?></div>
            </td>
            <td class="text-center"><?php echo $product['name']; ?><br /> <?php echo $product['price']; ?></td>
            <td class="text-right"><?php echo $date_added; ?></td>
          </tr>
          <?php } ?>
          <?php foreach($total_data as $total) { ?>
          <tr>
            <td class="text-right" colspan="2">
              <b><?php echo $total['title']; ?> :</b><br />
            </td>
            <td class="text-right">
              <?php echo $total['text']; ?><br />
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <p><?php echo $setting_invoice; ?></p>
      <br />
      <br />
      <br />
      <p>User's Signature ________________</p>
    </div>
  </div>
  




  <!--<?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always;">
    <div class="col-sm-12">   
	    <img src="<?php echo $storelogo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" style="margin-bottom:20px; padding-top:10px"/>
	   	<h2><?php echo $text_invoice; ?> #<?php echo $order['order_id']; ?></h2>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td colspan="2"><?php echo $text_order_detail; ?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 50%;">
              <address>
                <strong><?php echo $order['store_name']; ?></strong><br />
                <?php echo $order['store_address']; ?>
              </address>
              <b><?php echo $text_telephone; ?></b> <?php echo $order['store_telephone']; ?><br />
              <?php if ($order['store_fax']) { ?>
              <b><?php echo $text_fax; ?></b> <?php echo $order['store_fax']; ?><br />
              <?php } ?>
              <b><?php echo $text_email; ?></b> <?php echo $order['store_email']; ?><br />
              <b><?php echo $text_website; ?></b> <?php echo $order['store_url']; ?>
            </td>
            <td style="width: 50%;">
              <b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
              <?php if ($order['invoice_no']) { ?>
              <b><?php echo $text_invoice_no; ?></b> <?php echo $order['invoice_no']; ?><br />
              <?php } ?>
              <b><?php echo $text_order_id; ?></b> <?php echo $order['order_id']; ?><br />
              <b><?php echo $text_payment_method; ?></b> <?php echo $order['payment_method']; ?><br />
              <?php if ($order['shipping_method']) { ?>
              <b><?php echo $text_shipping_method; ?></b> <?php echo $order['shipping_method']; ?><br />
              <?php } ?>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td style="width: 50%;"><b><?php echo $text_payment_address; ?></b></td>
            <td style="width: 50%;"><b><?php echo $text_shipping_address; ?></b></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <address>
                <?php echo $order['payment_address']; ?>
              </address>
            </td>
            <td>
              <address>
                <?php echo $order['shipping_address']; ?>
              </address>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td><b><?php echo $column_product; ?></b></td>
            <td><b><?php echo $column_model; ?></b></td>
            <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
            <td class="text-right"><b><?php echo $column_price; ?></b></td>
            <td class="text-right"><b><?php echo $column_total; ?></b></td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order['product'] as $product) { ?>
          <tr>
            <td>
              <?php echo $product['name']; ?>
              <?php foreach ($product['option'] as $option) { ?>
              <br />
              &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
              <?php } ?>
            </td>
            <td><?php echo $product['model']; ?></td>
            <td class="text-right"><?php echo $product['quantity']; ?></td>
            <td class="text-right"><?php echo $product['price']; ?></td>
            <td class="text-right"><?php echo $product['total']; ?></td>
          </tr>
          <?php } ?>
          <?php foreach ($order['voucher'] as $voucher) { ?>
          <tr>
            <td><?php echo $voucher['description']; ?></td>
            <td></td>
            <td class="text-right">1</td>
            <td class="text-right"><?php echo $voucher['amount']; ?></td>
            <td class="text-right"><?php echo $voucher['amount']; ?></td>
          </tr>
          <?php } ?>
          <?php foreach ($order['total'] as $total) { ?>
          <tr>
            <td class="text-right" colspan="4"><b><?php echo $total['title']; ?></b></td>
            <td class="text-right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php if ($order['comment']) { ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td><b><?php echo $text_comment; ?></b></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $order['comment']; ?></td>
          </tr>
        </tbody>
      </table>
      <?php } ?>
    </div>
  </div>
  <?php } ?>-->
</div>

</body>
</html>
<script type="text/javascript">
 window.//print();
</script>


<style>
.table tr td{
  border-top:none !important;
} 
.table .des td{
  border-top:1px dashed #ddd !important;
}
.table .des1 td{
  border-bottom:1px dashed #ddd;
}  
.table thead  tr td:last-child, .table .des td:last-child{
  vertical-align: bottom;
}
</style>