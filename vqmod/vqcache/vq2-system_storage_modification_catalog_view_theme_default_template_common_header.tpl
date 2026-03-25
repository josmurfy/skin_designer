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
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/RotonProductDiscount.css" />
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

<script type="text/javascript" data-cfasync="false">
 var _foxpush = _foxpush || [];
 _foxpush.push(['_setDomain', 'phoenixliquidationca']);
 (function(){
     var foxscript = document.createElement('script');
     foxscript.src = '//cdn.foxpush.net/sdk/foxpush_SDK_min.js';
     foxscript.type = 'text/javascript';
     foxscript.async = 'true';
     var fox_s = document.getElementsByTagName('script')[0];
     fox_s.parentNode.insertBefore(foxscript, fox_s);})();
 </script>
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

        <?php $vie_front = Vie_Front::$instance; ?>
        <?php echo $vie_front->renderResources('header_styles'); ?>
        <style id="vie-theme-editor-style"><?php echo $vie_front->config->get('vie_theme_editor_skin_css'); ?></style>
        <?php if ($vie_front->getSkinOption('enable_custom_css') && $vie_front->getSkinOption('custom_css')) { ?>
        <style><?php echo $vie_front->getSkinOption('custom_css'); ?></style>
        <?php } ?>
      

        <script type="text/javascript">
          // we are using this flag to determine if the pixel
          // is successfully added to the header
          window.isFacebookPixelInHeaderAdded = 1;
          window.isFacebookPixelAdded=1;
        </script>

        <script type="text/javascript">
          function facebook_loadScript(url, callback) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            if(script.readyState) {  // only required for IE <9
              script.onreadystatechange = function() {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                  script.onreadystatechange = null;
                  if (callback) {
                    callback();
                  }
                }
              };
            } else {  //Others
              if (callback) {
                script.onload = callback;
              }
            }

            script.src = url;
            document.getElementsByTagName("head")[0].appendChild(script);
          }
        </script>

        <script type="text/javascript">
          (function() {
            var enableCookieBar = '<?php echo $facebook_enable_cookie_bar ?>';
            if (enableCookieBar === 'true') {
              facebook_loadScript("catalog/view/javascript/facebook/cookieconsent.min.js");

              // loading the css file
              var css = document.createElement("link");
              css.setAttribute("rel", "stylesheet");
              css.setAttribute("type", "text/css");
              css.setAttribute(
                "href",
                "catalog/view/theme/css/facebook/cookieconsent.min.css");
              document.getElementsByTagName("head")[0].appendChild(css);

              window.addEventListener("load", function(){
                function setConsent() {
                  fbq(
                    'consent',
                    this.hasConsented() ? 'grant' : 'revoke'
                  );
                }
                window.cookieconsent.initialise({
                  palette: {
                    popup: {
                      background: '#237afc'
                    },
                    button: {
                      background: '#fff',
                      text: '#237afc'
                    }
                  },
                  cookie: {
                    name: fbq.consentCookieName
                  },
                  type: 'opt-out',
                  showLink: false,
                  content: {
                    allow: <?php echo $cookie_bar_opt_in_value ?>,
                    deny: <?php echo $cookie_bar_opt_out_value ?>,
                    header: <?php echo $cookie_bar_header_value ?>,
                    message: <?php echo $cookie_bar_description_value ?>
                  },
                  layout: 'basic-header',
                  location: true,
                  revokable: true,
                  onInitialise: setConsent,
                  onStatusChange: setConsent,
                  onRevokeChoice: setConsent
                }, function (popup) {
                  // If this isn't open, we know that we can use cookies.
                  if (!popup.getStatus() && !popup.options.enabled) {
                    popup.setStatus(cookieconsent.status.dismiss);
                  }
                });
              });
            }
          })();
        </script>

        <script type="text/javascript">
          (function() {
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');

            var enableCookieBar = '<?php echo $facebook_enable_cookie_bar ?>';
            if (enableCookieBar === 'true') {
              fbq.consentCookieName = 'fb_cookieconsent_status';

              (function() {
                function getCookie(t){var i=("; "+document.cookie).split("; "+t+"=");if(2==i.length)return i.pop().split(";").shift()}
                var consentValue = getCookie(fbq.consentCookieName);
                fbq('consent', consentValue === 'dismiss' ? 'grant' : 'revoke');
              })();
            }

            <?php if ($facebook_pixel_id_FAE) { ?>
// system auto generated facebook_pixel.js, DO NOT MODIFY
pixel_script_filename = 'catalog/view/javascript/facebook/facebook_pixel_2_2_1.js';
// system auto generated facebook_pixel.js, DO NOT MODIFY
              facebook_loadScript(
                pixel_script_filename,
                function() {
                  var params = <?php echo $facebook_pixel_params_FAE ?>;
                  _facebookAdsExtension.facebookPixel.init(
                    '<?php echo $facebook_pixel_id_FAE ?>',
                    <?php echo $facebook_pixel_pii_FAE ?>,
                    params);
                  <?php if ($facebook_pixel_event_params_FAE) { ?>
                    _facebookAdsExtension.facebookPixel.firePixel(
                      JSON.parse('<?php echo $facebook_pixel_event_params_FAE ?>'));
                  <?php } ?>
                });
            <?php } ?>
          })();
        </script>
      
<?php echo $smartsearch_livesearch; ?>
</head>

                
<div id="HeaderNotification" style=" background-color:#FF9900; z-index:99999;font-size:20px; text-align:center; color:#fff; position:fixed;width:100%;height:28px;top:0px;">You are on DEMO site. No orders will be processed!</div>
<body 
                
             class="<?php echo $class; ?>">

        <div id="page-wrapper" class="<?php echo $vie_front->getSkinOption('layout_type'); ?>">
        <?php $registry = Vie_Front::$instance->registry;; $vie_module_groups = $registry->get('vie_module_groups'); if (!empty($vie_module_groups['vie_ht'])) { ?>
        <section><?php echo implode('', $vie_module_groups['vie_ht']); ?></section>
        <?php } ?>
      

        <header>

				<?php echo $awesome_social_login_ozxmod; ?>
             
          <nav id="top">
      
  <div class="container">
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
            <li>
				<?php  ?>
				<?php if($config->get('awesome_social_login_ozxmod_status')) { ?>
				<a id="signuppopup"><?php echo $text_register; ?></a>
				<?php } else { ?>
				<a href="<?php echo $register; ?>"><?php echo $text_register; ?></a>
				<?php } ?>
             </li>
            <li>
				<?php if($config->get('awesome_social_login_ozxmod_status')) { ?>
				<a id="loginpopup"><?php echo $text_login; ?></a>
				<?php } else { ?>
				<a href="<?php echo $login; ?>"><?php echo $text_login; ?></a>
				<?php } ?>
             </li>
            <?php } ?>
          </ul>
        </li>
        <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
        <li><a href="<?php echo $shopping_cart; ?>" title="<?php echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_shopping_cart; ?></span></a></li>
        <li><a href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_checkout; ?></span></a></li>
      </ul>
    </div>
  </div>
</nav>

  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <div id="logo">
          <?php if ($logo) { ?>
          <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" /></a>
          <?php } else { ?>
          <h1><a href="<?php echo $home; ?>"><?php echo $name; ?></a></h1>
          <?php } ?>
        </div>
      </div>
      <div class="col-sm-5"><?php echo $search; ?>
      </div>
      <div class="col-sm-3"><?php echo $cart; ?></div>
    </div>
  </div>


        <?php $registry = Vie_Front::$instance->registry;; $vie_module_groups = $registry->get('vie_module_groups'); if (!empty($vie_module_groups['vie_menu'])) { ?>
        <?php echo implode('', $vie_module_groups['vie_menu']); ?>
        <?php } else if ($categories) { ?>      
      
<div class="container">
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
  
</div>
        <?php } ?>

        <script type="text/javascript">
          // we are using this flag to determine if the customer chat
          // is successfully added to the header
          window.isFacebookCustomerChatInHeaderAdded = 1;
          window.isFacebookCustomerChatAdded=1;
        </script>

        <?php if ($facebook_messenger_enabled_FAE == 'true') { ?>
        <!-- Facebook JSSDK -->
        <script>
          window.fbAsyncInit = function() {
            FB.init({
              appId            : '',
              autoLogAppEvents : true,
              xfbml            : true,
              version          : '<?php echo $facebook_jssdk_version_FAE ?>'
            });
          };

          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/<?php echo $facebook_customization_locale_FAE ?>/sdk/xfbml.customerchat.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, 'script', 'facebook-jssdk'));
        </script>
        <div
          id="fb-customerchat-header"
          class="fb-customerchat"
          attribution="fbe_opencart"
          page_id="<?php echo $facebook_page_id_FAE ?>"
          <?php echo $facebook_customization_greeting_text_code_FAE ?>
          <?php echo $facebook_customization_theme_color_code_FAE ?>
        />
        <?php } ?>
      
      </header>
      <?php $registry = Vie_Front::$instance->registry;; $vie_module_groups = $registry->get('vie_module_groups'); ?>
      <?php if (!empty($vie_module_groups['vie_fw_promo'])) { ?>
      <div id="vie-promotion-top"><?php echo implode('', $vie_module_groups['vie_fw_promo']); ?></div>
      <?php } ?>
      <div id="vie-promotion-content-container">
        <div class="container">
          <div class="row">
            <?php if (!empty($vie_module_groups['vie_pm_top'])) { ?>
              <div id="vie-promotion-top" class="col-sm-12">
              <?php echo implode('', $vie_module_groups['vie_pm_top']); ?>
            </div>
            <?php } ?>

            <?php if (!empty($vie_module_groups['vie_pm_left'])) { ?>
              <column id="vie-promotion-left" class="col-sm-3">
                <?php echo implode('', $vie_module_groups['vie_pm_left']); ?>
              </column>
            <?php } ?>

            <?php if (!empty($vie_module_groups['vie_pm_left']) && !empty($vie_module_groups['vie_pm_right'])) { ?>
              <?php $promo_class = 'col-sm-6'; ?>
            <?php } elseif (!empty($vie_module_groups['vie_pm_left']) || !empty($vie_module_groups['vie_pm_right'])) { ?>
              <?php $promo_class = 'col-sm-9'; ?>
            <?php } else { ?>
              <?php $promo_class = 'col-sm-12'; ?>
            <?php } ?>

            <?php if (!empty($vie_module_groups['vie_pm_content'])) { ?>
              <div id="vie-promotion-content" class="<?php echo $promo_class; ?>">
                <?php echo implode('', $vie_module_groups['vie_pm_content']); ?>
              </div>
            <?php } ?>

            <?php if (!empty($vie_module_groups['vie_pm_right'])) { ?>
              <column id="vie-promotion-right" class="col-sm-3">
                <?php echo implode('', $vie_module_groups['vie_pm_right']); ?>
              </column>
            <?php } ?>

            <?php if (!empty($vie_module_groups['vie_pm_bottom'])) { ?>
              <div id="vie-promotion-bottom" class="col-sm-12">
              <?php echo implode('', $vie_module_groups['vie_pm_bottom']); ?>
            </div>
            <?php } ?>
          </div>
        </div>   
      </div>
