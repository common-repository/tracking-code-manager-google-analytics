<?php
class Google_Analytics_Tracking_Code_Manager_Admin {

    private $version;

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function enqueue_styles() {

        wp_enqueue_style(
            'google-analytics-tracking-code-admin',
            plugin_dir_url( __FILE__ ) . 'css/google-analytics-tracking-code-admin.css',
            array(),
            $this->version,
            FALSE
        );

    }
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Google Analytics Settings',
            'Google Analytics Settings',
            'manage_options',
            'gac-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'gac_option_name' );
        ?>
        <div class="wrap">
            <h1>Google Analytics Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'gac_option_group' );
                do_settings_sections( 'gac-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'gac_option_group', // Option group
            'gac_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Google Analytics Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'gac-setting-admin' // Page
        );

        add_settings_field(
            'gac_tracking_id', // ID
            'Google Analytics Tracking ID', // Title
            array( $this, 'gac_tracking_id_setting_callback' ), // Callback
            'gac-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'tracking_code_position',
            'Tracking Code position',
            array( $this, 'tracking_code_position_callback' ),
            'gac-setting-admin',
            'setting_section_id'
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['gac_tracking_id'] ) )
            $new_input['gac_tracking_id'] = sanitize_text_field( $input['gac_tracking_id'] );

        if( isset( $input['tracking_position'] ) )
            $new_input['tracking_position'] = sanitize_text_field( $input['tracking_position'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function gac_tracking_id_setting_callback()
    {
        printf(
            '<input type="text" id="gac_tracking_id" name="gac_option_name[gac_tracking_id]" value="%s" />',
            isset( $this->options['gac_tracking_id'] ) ? esc_attr( $this->options['gac_tracking_id']) : ''
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function tracking_code_position_callback()
    {
        $position = 'header';
        if (isset( $this->options['gac_tracking_id'] )){

            if( ($this->options['tracking_position'] == 'footer' )) {
                $position = 'footer';
            }
        }
      ?>

        <lable for="tracking_pos_header">
            <input type="radio" id="tracking_pos_header" name="gac_option_name[tracking_position]" value="header"
                <?php  if( $position =='header' ) { echo 'checked'; } ?>  />
            Header</lable>

        <lable for="tracking_pos_footer">
             <input type="radio" id="tracking_pos_footer" name="gac_option_name[tracking_position]" value="footer"
                 <?php  if( $position =='footer' ) { echo 'checked'; } ?>  />
              Footer</lable>
      <?php
    }

    public function add_meta_box() {

        add_meta_box(
            'google-analytics-tracking-code-admin',
            'Google Analytics Tracking Code',
            array( $this, 'render_meta_box' ),
            'page',
            'normal',
            'core'
        );

    }

    public function save_meta_box() {

        global $post;

        // verify meta box nonce
        if ( !isset( $_POST['google_analytics_tracking_code'] ) || !wp_verify_nonce( $_POST['google_analytics_tracking_code'], 'google_analytics_tracking_nonce' ) ){
            return;
        }
        // return if autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
            return;
        }
        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $post->ID ) ){
            return;
        }

        if( isset( $_POST['_disable_ga_tracking'] ) ){
            $track_enabled =  sanitize_text_field($_POST['_disable_ga_tracking']);

            // save data
            update_post_meta( $post->ID, '_disable_ga_tracking', $track_enabled );
        }else{
            // delete data
            delete_post_meta( $post->ID, '_disable_ga_tracking' );
        }

    }

    public function render_meta_box() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/google-analytics-tracking-code-manager.php';
    }

}