<div id="google-analytics-tracking-code">
   <?php
    global $post;
    $enable_tracking = get_post_meta($post->ID, '_disable_ga_tracking', true);
    ?>
    <?php wp_nonce_field( 'google_analytics_tracking_nonce', 'google_analytics_tracking_code' ); ?>

    <input type="checkbox" name="_disable_ga_tracking" value="1" class="_disable_ga_tracking" <?php
        if ($enable_tracking){echo 'checked'; }?>/> Disallow Google to track this page
</div>