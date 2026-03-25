<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>" type="text/javascript"></script>





<script type="text/javascript">

    function reCaptcha3() {

        grecaptcha.ready(function() {

            grecaptcha.execute('<?php echo $site_key; ?>', {action:'validate_captcha'})

                .then(function(token) {

                    document.getElementById('g-recaptcha-response').value = token;

                });

        });

    }

</script>





<fieldset>

    <div class="form-group required">

        <?php if (substr($route, 0, 9) == 'checkout/') { ?>

            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

            <input type="hidden" name="action" value="validate_captcha">

            <?php if ($error_captcha) { ?>

                <div class="text-danger"><?php echo $error_captcha; ?></div>

            <?php } ?>

        <?php } else { ?>

            <div class="col-sm-12">

                <script type="text/javascript">

                    if (typeof(reCaptcha3) === 'function') {

                        reCaptcha3();

                    }</script>

                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                <input type="hidden" name="action" value="validate_captcha">

                <?php if ($error_captcha) { ?>

                    <div class="text-danger"><?php echo $error_captcha; ?></div>

                <?php } ?>

            </div>

        <?php } ?>

    </div>

</fieldset>





