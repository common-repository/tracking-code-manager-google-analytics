<?php

class Google_Analytics_Tracking_Code_Manager {

    protected $loader;

    protected $plugin_slug;

    protected $version;

    public function __construct() {

        $this->plugin_slug = 'google-analytics-tracking-manager-slug';
        $this->version = '0.1.0';
        $this->options = get_option( 'gac_option_name' );

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_frontend_hooks();

    }

    private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-google-analytics-tracking-code-admin.php';

        require_once plugin_dir_path( __FILE__ ) . 'class-google-analytics-tracking-code-loader.php';
        $this->loader = new Google_Analytics_Tracking_Code_Loader();

    }

    private function define_admin_hooks() {

        $admin = new Google_Analytics_Tracking_Code_Manager_Admin( $this->get_version() );

        $this->loader->add_action( 'admin_menu', $admin, 'add_plugin_page' );
        $this->loader->add_action( 'admin_init', $admin, 'page_init' );

        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );
        $this->loader->add_action( 'save_post', $admin, 'save_meta_box' );


    }

    private function define_frontend_hooks() {

        $tracking_position = esc_attr( $this->options['tracking_position']);

        if ($tracking_position == 'footer'){
            // include GA tracking code before the closing body tag
            $this->loader->add_action('wp_footer', $this,'google_analytics_tracking_code' );
        } else {
            // include GA tracking code before the closing head tag
            $this->loader->add_action('wp_head', $this,'google_analytics_tracking_code' );
        }

    }

    // Include the Google Analytics Tracking Code (ga.js)
   // @ https://developers.google.com/analytics/devguides/collection/gajs/
    public function google_analytics_tracking_code(){
        global $post;
        $disable_tracking = get_post_meta($post->ID, '_disable_ga_tracking', true);

        // check if it disabled from page settings
        if (!$disable_tracking) {
            $propertyID = esc_attr( $this->options['gac_tracking_id']); // GA Property ID

            if ($propertyID) { ?>

            <script type="text/javascript">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', '<?php echo $propertyID; ?>']);
                _gaq.push(['_trackPageview']);

                (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();
            </script>

        <?php } }
    }

    public function run() {
        $this->loader->run();
    }

    public function get_version() {
        return $this->version;
    }

}
