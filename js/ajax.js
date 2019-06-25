jQuery(document).ready(function() {
    jQuery("input").click(function ($) {
        var sync_name = jQuery(this).attr('name');
        jQuery.get("https://magtap.satellite.fm/wp-content/plugins/tapgenie-sync-plugin/run_sync.php" + "?" + sync_name, function (data, status) {
            jQuery("#response").html("<span class='red'>" + data + "</span>");
        });
    });
});
