<div id="turnstile-captcha"></div>
<input type="hidden" name="turnstile_response" id="turnstile-response" />

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer onload="initializeTurnstile()"></script>

<script type="text/javascript">
    function initializeTurnstile() {
        const container = document.getElementById("turnstile-captcha");
        if (container) {
            turnstile.render(container, {  <!-- Passer l'élément HTML directement -->
                sitekey: "<?php echo $site_key; ?>",
                callback: function(token) {
                    document.getElementById("turnstile-response").value = token;
                }
            });
        } else {
            console.error("Élément de conteneur non trouvé.");
        }
    }
</script>
