<?php echo $header; ?> <?php echo $column_left; ?>
<div id="content"> 
  <div class="page-header">
    <div class="container-fluid">      
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="wrap Configuration">
    <?php
      // Rendering message/error-message as a wp-message if any.
      if(isset($message) && strlen($message) > 0):
      ?>
    <div id="message" class="updated mag_message_box" style="font-size:16px;">
      <p class="mag_message_box_text"><?php echo $message; ?></p>
    </div>
    <?php elseif (isset($error_message) && strlen($error_message) > 0): ?>
    <div id="message" class="error mag_message_box">
      <p class="mag_message_box_text"><?php echo $error_message; ?></p>
    </div>
    <?php endif; ?>

    <div class="feedify-container">
      <?php if (!$m_license_code_old || $m_license_code_old === ''){ ?>
      <div class="feedify-form-container">
        <div class="feedify-form inline-forms">
          <div id="feedify-loading-info">Loading, please wait ...</div>
          <iframe src="<?php echo $src_url; ?>" class="feedify-iframe signup-iframe" id="we-signup-iframe" marginheight="0" frameborder="0" style="background-color:transparent;" allowTransparency="true"></iframe>
        </div>
        <div class=" inline-forms" id="form2">          
          <form method="post" action="<?php echo $main_url; ?>" id="feedify_form"> 
            <input type="hidden" name="feedify_license_code" value="1"/>
            <input type="hidden" value="wp-save" name="weAction"/>
            <input type="hidden" value="true" name="noheader"/>
            <input type="hidden" value="main" name="page" />
            <input type="hidden" value="<?php echo $token; ?>" name="token" />            
          </form>
        </div>
      </div>
      <div class="feedify-form-container" id="login-form">
        <div class="feedify-form inline-forms">
          <div class="login-div">
            <div class="login-inner">
              <h3>Already a Feedify user? Login here</h3>            
              <div class=" inline-forms" id="">
                <div id="message" class="updated mag_message_box login_error" style="font-size:16px;display:none;">
                  <p class="mag_message_box_text" style="margin: 7px 0px;"></p>
                </div>
                <form action="" method="post" id="form-login">
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" name="login" placeholder="Email" autocomplete="off">
                      <input type="password" name="password" placeholder="Password" autocomplete="off">
                      <button type="button" id="button-login">Login</button>
                      <a href="https://feedify.net/forgot_password" target="_blank" style="text-decoration: none;color: #000;font-size: 15px;font-family: 'CALIBRIB_5';top: -3px;left: 10px;position: relative;">Forget Password | Need Help?</a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>      
        </div>
      </div>
      <?php }else{ ?>
      <div class="feedify-form-container">
        <div class="feedify-form inline-forms">
          <form method="post" action="<?php echo $main_url; ?>">
            <div class="">
              <label for="feedify_license_code" style="font-size: 21px;line-height: 45px;">
                <b>Awesome! Your Feedify license is activated.</b>
              </label>
              <input id="feedify_license_code" type="hidden" name="feedify_license_code" value="<?php echo $m_license_code_old; ?>"/>
            </div>            
            <input type="hidden" value="wp-save" name="weAction"/>
            <input type="hidden" value="true" name="noheader" />
            <input type="hidden" value="<?php echo $module_route; ?>" name="route" />
            <input type="hidden" value="<?php echo $we_page; ?>" name="page" />
            <input type="hidden" value="<?php echo $session_token; ?>" name="token" />           
          </form>          
        </div>
        <div class="feedify-login-n">
          <div class="center">
            <a href="https://feedify.net/login" target="_blank" class="btn btn-feedify">Click here to login Now</a>
            <p>Start with Push Notification, Create Pop-ups, Surveys, Campaigns to engage your customer better</p>
          </div>
        </div>
        <style>
          .feedify-login-n{
            text-align: center;
            border: 1px solid #f1f1f1;
            padding: 51px 0px;
            background-color: #fbfbfb;
          }
          .btn-feedify{
            background-color: #e48629;
            text-decoration: none;
            font-size: 20px;
            padding: 11px 39px;
            border: 1px solid #e48629;
            color: #fff;
            display: inline-block;
            text-decoration: none!important;
            margin-bottom: 15px;
          }
          .btn-feedify:hover, .btn-feedify:focus{
            color: #fff;
          }
        </style>
      </div>
      <script>
        window.onload = function() {
          var resendLinks = document.getElementsByTagName('a');
          for(var i = 0; i < resendLinks.length; i++) {
            var resendLink = resendLinks[i];
            if(resendLink.className === 'resend-email-link') {
              resendLink.onclick = function() {
                var newFrame = document.createElement("iframe");
                newFrame.style.height = "0px";
                newFrame.setAttribute("marginheight", "0");
                newFrame.setAttribute("frameborder", "0");
                newFrame.setAttribute("src", "<?php echo $resend_email_url; ?>");
                document.body.appendChild(newFrame);
                return false;
              }
            }
          }
        }
      </script>
      <br class="clear"/>
      <?php } ?>
    </div>
    <script type="text/javascript">
      if (document.getElementById('we-signup-iframe')) {
        var resizeIframe = function (height) {
          document.getElementById('we-signup-iframe').style.height = (parseInt(height) + 40) + "px";
        };      
        if (typeof window['addEventListener'] !== 'undefined' && typeof window['postMessage'] !== 'undefined') {
          window.addEventListener("message", function (e) {            
            console.log(e.data);           
            if(typeof e.data.height !== 'undefined'){
              resizeIframe(e.data.height);
            }else if(typeof e.data.action !== 'undefined'){
              $('#feedify_form').submit();
            }
          }, false);
        }        
        document.getElementById('we-signup-iframe').onload = function () {
          if (typeof window['addEventListener'] === 'undefined' || typeof window['postMessage'] === 'undefined') {
            document.getElementById('we-signup-iframe').style.height = "450px";
          }
          setTimeout(function () {
            if (document.getElementById('feedify-loading-info')) {
              document.getElementById('feedify-loading-info').style.display = 'none';
            }
          }, 500);
        };
      }
    </script>
  </div>
</div>
<?php echo $footer; ?>
<script>
$("#button").click(function() {
  $('html, body').animate({
    scrollTop: $("#login-form").offset().top
  }, 2000);
});
$('#button-login').click(function(){
  $.ajax({
    url: 'https://feedify.net/thirdparty/signin',
    type:'post',
    data:$('#form-login').serialize(),
    dataType:'jsonp',
    beforeSend: function(){
      $('.login_error').hide();
      $('#button-login').button('loading');
    },
    complete: function(){
      $('#button-login').button('reset');
    },
    success: function(response){
      if(typeof response[0] !== 'undefined'){
        response = response[0];
        if(response.errors){
          $('.login_error').show();        
          var html = response.errors.login;
          if(typeof response.errors.banned !== 'undefined')
          html += '<br>'+response.errors.banned;
          if(typeof response.errors.not_activated !== 'undefined')
          html += '<br>'+response.errors.not_activated
          $('.login_error p').html(html);
        }else if(response.success ==  'ok'){
          $('#feedify_form').submit();
        }else{
          $('.login_error').show();
          $('.login_error p').html('Unknown error!');
        }
      }
    }
  })
})
</script>