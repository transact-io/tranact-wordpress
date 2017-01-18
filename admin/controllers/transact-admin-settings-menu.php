<?php
namespace Transact\Admin\Settings\Menu;

use Transact\Utils\Config\Parser\ConfigParser;
require_once  plugin_dir_path(__FILE__) . '/../../utils/transact-utils-config-parser.php';

use Transact\Admin\Api\TransactApi;
require_once  plugin_dir_path(__FILE__) . '/transact-api.php';


/**
 * Class AdminSettingsMenuExtension
 */
class AdminSettingsMenuExtension
{
    /**
     * All hooks to dashboard
     */
    public function hookToDashboard()
    {
        add_action( 'admin_menu', array( $this, 'add_transact_menu' ));
        add_action( 'admin_init', array( $this, 'register_transact_settings' ));
        add_action( 'admin_init', array( $this, 'hook_post_settings_and_validates'));
    }

    /**
     * Creates Transact.io Menu on Dasboard
     */
    public function add_transact_menu()
    {
        add_menu_page( 'transact.io', 'transact.io', 'manage_options', 'transact-admin-page.php', array($this, 'transact_io_admin_callback'), 'dashicons-cart' );
    }

    /**
     * Callback for the Transact.io menu (one generating the thml output)
     */
    public function transact_io_admin_callback()
    {
        ob_start();
        require plugin_dir_path(__FILE__) . '/../views/transact-account-view.php';
        ob_end_flush();
    }

    /**
     * Registering Settings on transact account settings
     */
    public function register_transact_settings()
    {
        // API Transact Settings
        register_setting(
            'transact-settings',
            'transact-settings'
        );

        // API Transact Settings
        add_settings_section(
            'api_keys',
            __( 'API keys', 'transact' ),
            function() { _e('Log in into transact.io to find out about your credentials.','transact'); },
            'transact-settings'
        );

        // Adding Account ID field
        add_settings_field(
            'api_id',
            __( 'Account ID', 'transact' ),
            array($this, 'account_id_settings_callback'),
            'transact-settings',
            'api_keys',
            array('account_id')
        );

        // Adding Secret key field
        add_settings_field(
            'secret_key',
            __( 'Secret Key', 'transact' ),
            array($this, 'account_id_settings_callback'),
            'transact-settings',
            'api_keys',
            array('secret_key')
        );

        // Adding Account ID field
        add_settings_field(
            'environment',
            __( 'Environment', 'transact' ),
            array($this, 'account_id_settings_callback'),
            'transact-settings',
            'api_keys',
            array('environment')
        );

        /*
         * Post Types Manager
         */
        // API Transact Settings
        add_settings_section(
            'post_types',
            __( 'Post Types', 'transact' ),
            function() { _e('Enable Transact for Custom Post Types. By default Transact is available for posts and pages only.','transact'); },
            'transact-settings'
        );

        // Adding Account ID field
        add_settings_field(
            'custom_post_types',
            __( 'Custom Post Types', 'transact' ),
            array($this, 'custom_post_types_callback'),
            'transact-settings',
            'post_types',
            array('custom_post_types')
        );

        /*
         * Button Styles Manager
         */
        add_settings_section(
            'xct_button_style', // ID
            'Purchase Button Settings', // Title
            function() { _e('You can customize the visual appearance of the Purchase on Transact button here.','transact'); },
            'transact-settings'
        );  

        add_settings_field(
            'text_color', // ID
            'Text Color', // Title 
            array( $this, 'color_input_callback' ), // Callback
            'transact-settings',
            'xct_button_style', // Section,
            array('text_color') // Default value
        );      

        add_settings_field(
            'background_color', 
            'Background Color', 
            array( $this, 'color_input_callback' ), 
            'transact-settings', 
            'xct_button_style',
            array('background_color', '#308030') // Default value
        );

    }

    /**
     * CPT Settings callback
     * It will show all visible cpt and make the user select the ones they want transact on.
     *
     * @param $arg
     */
    public function custom_post_types_callback($arg)
    {
        $public_post_types = get_post_types(array('public' => true));

        /*
         * Wordpress will include by default post, page, attachment
         * as Transact will have by default post and page, we avoid them
         */
        unset($public_post_types['post']);
        unset($public_post_types['page']);
        unset($public_post_types['attachment']);

        $options = get_option('transact-settings');
        $cpt_options = isset($options['cpt']) ? $options['cpt'] : array();
        ?>
        <script>
            // Handles checkbox for cpt
            function setValue(id) {
                if( jQuery(id).is(':checked')) {
                    jQuery(id).val(1);
                } else {
                    jQuery(id).val(0);
                }
            }
        </script>
        <table>
            <tr>
                <?php foreach ($public_post_types as $key => $cpt): ?>
                    <?php
                        $cpt_selected = '';
                        $checkbox_value = 0;
                    
                        if ($cpt_options) {
                            $cpt_selected = ( (isset($cpt_options['cpt_' . $key])) && ($cpt_options['cpt_' . $key] == 1) ) ? 'checked' : '';
                            $checkbox_value = ( $cpt_selected == 'checked') ? 1 : 0;
                        }
                    ?>
                <td>
                    <input <?php echo $cpt_selected; ?> type="checkbox" onclick="setValue(cpt_<?php echo $key;?>)" id="cpt_<?php echo $key;?>" name="transact-settings[cpt][cpt_<?php echo $key;?>]" value="<?php echo $checkbox_value; ?>" /><?php echo $key;?>
                </td>
                <?php endforeach; ?>
            </tr>
        </table>
        <?php
    }

    /**
     * Individual settings callback to be created
     * @param $arg
     */
    public function account_id_settings_callback($arg)
    {
        $arg = current($arg);
        $options = get_option('transact-settings');

        /**
         * If it is environment it is a dropdown input
         */
        if ($arg == 'environment') {
            ?>
                <select name=transact-settings[<?php echo $arg;?>]>
                    <option <?php if ($options[$arg] == 'prod') echo 'selected'; ?> value="prod">Production</option>
                    <option <?php if ($options[$arg] == 'test') echo 'selected'; ?> value="test">Test</option>
                </select>
            <?php
        } else {
            echo "<input name='transact-settings[$arg]' type='text' value='{$options[$arg]}' style='width: 300px'/>";
        }
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function color_input_callback($field, $default_color = NULL)
    {
        $field = current($field);
        if(is_null($default_color)) {
            $default_color = '#ffffff';
        } else {
            $default_color = current($default_color);
        }
        $options = get_option('transact-settings');

        printf(
            '<input type="color" id="%s" name="transact-settings[%s]" value="%s" />',
            $field, 
            $field,
            isset( $options[$field] ) ? esc_attr( $options[$field]) : $default_color
        );
    }

    /**
     * Gets Account ID from Settings
     *
     * @return string
     */
    public function get_account_id()
    {
        $options = get_option('transact-settings');
        return $options['account_id'];
    }

    /**
     * Gets Secret from Settings
     * @return string
     */
    public function get_secret()
    {
        $options = get_option('transact-settings');
        return $options['secret_key'];
    }

    /**
     * Gets environment from Settings
     * @return string
     */
    public function get_env()
    {
        $options = get_option('transact-settings');
        return $options['environment'];
    }

    /**
     * Gets css settings from Settings
     * @return string
     */
    public function get_button_style()
    {
        $options = get_option('transact-settings');
        return array(
            'text_color' => (isset($options['text_color']) ? $options['text_color'] : ''),
            'background_color' => (isset($options['background_color']) ? $options['background_color'] : ''),
        );
    }

    /**
     * Hook on Settings page when POST
     * We check if the credentials are good, in that case we set a flag to know it in the future
     * In case they are wrong, we set the flag to false and show a message to the publisher
     *
     */
    public function hook_post_settings_and_validates()
    {
        if (isset($_POST['option_page']) && ($_POST['option_page'] == 'transact-settings'))
        {
            $_POST['transact-settings'] = filter_var_array($_POST['transact-settings'], FILTER_SANITIZE_STRING);
            $validate_url = (new ConfigParser())->getValidationUrl();
            $response = (new TransactApi())->validates($validate_url, $_POST['transact-settings']['account_id'], $_POST['transact-settings']['secret_key']);
            if ($response) {
                set_transient( SETTING_VALIDATION_TRANSIENT, 1, 0);
            } else {
                set_transient( SETTING_VALIDATION_TRANSIENT, 0, 0);
            }
        }
    }

}

