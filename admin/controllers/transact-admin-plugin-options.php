<?php
class TransactSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Transact.io Settings', 
            'manage_options', 
            'transact-settings-page', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'transact_settings' );

        if(isset($_REQUEST['transact_settings'])) {
            update_option( 'transact_settings', $_REQUEST['transact_settings']);
        }
        ?>
        <div class="wrap">
            <h1>Transact.io Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'transact_settings_group' );
                do_settings_sections( 'transact-settings-page' );
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
            'transact_settings_group', // Option group
            'transact_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Purchase Button Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'transact-settings-page' // Page
        );  

        add_settings_field(
            'text_color', // ID
            'Text Color', // Title 
            array( $this, 'text_color_callback' ), // Callback
            'transact-settings-page', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'background_color', 
            'Background Color', 
            array( $this, 'background_color_callback' ), 
            'transact-settings-page', 
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
        if( isset( $input['text_color'] ) )
            $new_input['text_color'] = sanitize_text_field( $input['text_color'] );

        if( isset( $input['background_color'] ) )
            $new_input['background_color'] = sanitize_text_field( $input['background_color'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'You can customize the visual appearance of the Purchase on Transact button here.';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function text_color_callback()
    {
        printf(
            '<input type="color" id="text_color" name="transact_settings[text_color]" value="%s" />',
            isset( $this->options['text_color'] ) ? esc_attr( $this->options['text_color']) : '#ffffff'
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function background_color_callback()
    {
        printf(
            '<input type="color" id="background_color" name="transact_settings[background_color]" value="%s" />',
            isset( $this->options['background_color'] ) ? esc_attr( $this->options['background_color']) : '#308030'
        );
    }
}

if( is_admin() )
    $my_settings_page = new TransactSettingsPage();