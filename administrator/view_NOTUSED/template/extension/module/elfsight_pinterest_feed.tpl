<?php echo $header; ?><?php echo $column_left; ?> 

<div class="elfsight-portal-embed">
    <iframe src="<?php echo $url; ?>"></iframe>    
</div>

<script>
    jQuery(function () {
        window.onmessage = function (e) {
            if (e.data && e.data.params) {
                jQuery.ajax({
                    type: 'POST',
                    url: '',
                    data: {
                        params: e.data.params
                    },
                    success: function (response) {
                    }
                })
            }
        };
    });
</script>

<style>
    .elfsight-portal-embed{
        position: absolute;
        margin-left: 50px;
        width: calc(100% - 50px);
        height: calc(100% - 50px);
        overflow: hidden;
    }
    
    .elfsight-portal-embed iframe {
        width: 100%;
        height: 100%;
        overflow: hidden;
        border: none;
    }
</style>