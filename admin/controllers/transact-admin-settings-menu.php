<?php
namespace Transact\Admin\Settings\Menu;

use Transact\Admin\Config\Parser\AdminConfigParser;
require_once  plugin_dir_path(__FILE__) . '/transact-admin-config-parser.php';

use Transact\Admin\Api\TransactApi;
require_once  plugin_dir_path(__FILE__) . '/transact-api.php';


/**
 * Class AdminSettingsMenuExtension
 */
class AdminSettingsMenuExtension
{
    /**
     * Transient that holds validation status
     */
    const SETTING_VALIDATION_TRANSIENT = 'setting_validation_transient';

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
            $validate_url = (new AdminConfigParser())->getValidationUrl();
            $response = (new TransactApi())->validates($validate_url, $_POST['transact-settings']['account_id'], $_POST['transact-settings']['secret_key']);
            if ($response) {
                set_transient( self::SETTING_VALIDATION_TRANSIENT, 1, 0);
            } else {
                set_transient( self::SETTING_VALIDATION_TRANSIENT, 0, 0);
            }
        }
    }

}

