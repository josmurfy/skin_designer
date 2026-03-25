<script type="text/javascript">
(function($) {
    $(document).ready(function() {
        $.ajax({
            url : '<?php echo $squareup_url; ?>',
            dataType : 'html',
            success : function(html) {
                $('#content > .container-fluid').prepend(html);
            }
        });
    });
})(jQuery);
</script>