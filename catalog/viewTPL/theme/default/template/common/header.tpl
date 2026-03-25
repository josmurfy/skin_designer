<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet">
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
<?php  
if ($lang=="en") {?>
s1.src='https://embed.tawk.to/5aee11a2227d3d7edc24fd0a/default';
<?php }
else if ($lang=="fr") {?>
s1.src='https://embed.tawk.to/5e3d77d4a89cda5a1884b851/default';
<?php }
else {?>
s1.src='https://embed.tawk.to/5aee11a2227d3d7edc24fd0a/default';
<?php }?>
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<link rel="apple-touch-icon" sizes="57x57" href="https://phoenixliquidation.ca/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="https://phoenixliquidation.ca/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="https://phoenixliquidation.ca/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="https://phoenixliquidation.ca/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="https://phoenixliquidation.ca/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="https://phoenixliquidation.ca/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="https://phoenixliquidation.ca/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="https://phoenixliquidation.ca/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="https://phoenixliquidation.ca/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="https://phoenixliquidation.ca/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://phoenixliquidation.ca/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="https://phoenixliquidation.ca/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://phoenixliquidation.ca/favicon-16x16.png">
<link rel="manifest" href="https://phoenixliquidation.ca/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="https://phoenixliquidation.ca/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
</head>
<body class="<?php echo $class; ?>">
<!--
<script type='text/javascript'>var script = document.createElement('script');
script.async = true; script.type = 'text/javascript';
var target = 'https://www.clickcease.com/monitor/stat.js';
script.src = target;var elem = document.head;elem.appendChild(script);
</script>
<noscript>
<a href='https://www.clickcease.com' rel='nofollow'><img src='https://monitor.clickcease.com/stats/stats.aspx' alt='ClickCease'/></a>
</noscript>
-->
<table style="width:100%;background-color:#FFFFFF">
  <tr><td >
 
    <div class="row">
      <div class="col-sm-4">
                 <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" /></a>
      </div>
      <div class="col-sm-5"><?php echo $search; ?>
      </div>
      <div class="col-sm-3"><?php echo $cart; ?></div>
    </div>

  </td></tr>
</table>
<nav id="top">


    <?php echo $currency; ?>
    <?php echo $language; ?>
    <div id="top-links" class="nav pull-right">
      <ul class="list-inline">
        <li><a href="<?php echo $contact; ?>"><i class="fa fa-phone"></i></a> <span class="hidden-xs hidden-sm hidden-md"><?php echo $telephone; ?></span></li>
        <li class="dropdown"><a href="<?php echo $account; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_account; ?></span> <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-right">
            <?php if ($logged) { ?>
            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
            <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
            <?php } ?>
          </ul>
        </li>
        <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
        <li><a href="<?php echo $shopping_cart; ?>" id="shopping-total" title="<?php echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_shopping_cart; ?></span></a></li>
        <li><a href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_checkout; ?></span></a></li>
      </ul>
    </div>

</nav>

<?php if ($categories) { ?>

  <nav id="menu" class="navbar">
    <div class="navbar-header"><span id="category" class="visible-xs"><?php echo $text_category; ?></span>
      <button type="button" class="btn btn-navbar navbar-toggle" data-bs-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <ul class="nav navbar-nav">
        <?php foreach ($categories as $category) { ?>
        <?php if ($category['children']) { ?>
        <li class="dropdown"><a href="<?php echo $category['href']; ?>" class="dropdown-toggle" data-bs-toggle="dropdown"><?php echo $category['name']; ?></a>
          <div class="dropdown-menu">
            <div class="dropdown-inner">
              <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
              <ul class="list-unstyled">
                <?php foreach ($children as $child) { ?>
                <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
                <?php } ?>
              </ul>
              <?php } ?>
            </div>
            <a href="<?php echo $category['href']; ?>" class="see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a> </div>
        </li>
        <?php } else { ?>
        <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
        <?php } ?>
        <?php } ?>
		<li><a href="<?php echo $electronics; ?>"><?php echo $text_electronics; ?></a></li>
		<li><a href="<?php echo $toys; ?>"><?php echo $text_toys; ?></a></li>
		<li><a href="<?php echo $videogamesnconsoles; ?>"><?php echo $text_videogamesnconsoles; ?></a></li>
		<li><a href="<?php echo $dvdsnmovies; ?>"><?php echo $text_dvdsnmovies; ?></a></li>
		<li><a href="<?php echo $cellphoneaccessories; ?>"><?php echo $text_cellphoneaccessories; ?></a></li>
      </ul>
    </div>
  </nav>


<br>
<br>
<br>
 
<?php } 
if(isset($slider)){
  echo $slider;
}
?>
