<?php
/*
 * Plugin Name: WP transact.io
 * Description: Integrates transact.io services into WP
 * Version: 0.0.4
 * Author: transact.io
 * Author URI: http://www.transact.io
 */

/**
 * Requires
 */
use PM\Freelance\Frontend\FrontendExtension;
use Transact\Admin\DashboardExtension;
require_once( plugin_dir_path(__FILE__) . 'admin/transact-admin.php' );

use Transact\FrontEnd\Controllers\Post\FrontEndPostExtension;
require_once( plugin_dir_path(__FILE__) . 'frontend/controllers/transact-single-post.php' );


/**
 * Define all constants
 */
define('TRANSACT_PATH', dirname( __FILE__ ));
define('CONFIG_PATH', dirname( __FILE__ ) . '/config.ini');
define('VENDORS_PATH', dirname( __FILE__ ) . '/vendors/');
define('VENDORS_URL', plugins_url('/vendors/transact-io-php/', __FILE__));
define('FRONTEND_ASSETS_URL', plugins_url('/frontend/assets/', __FILE__));

/**
 * Transient that holds validation status
 */
define('SETTING_VALIDATION_TRANSIENT', 'setting_validation_transient');


/**
 * Hooking functionality to Dashboard
 */
(new DashboardExtension())->hookToDashboard();

/**
 * Hooking functionality to Single Post Frontend
 */
(new FrontEndPostExtension())->hookSinglePost();


/**
 * On Activation: Create table
 */
register_activation_hook(__FILE__, 'transact_activation');
function transact_activation()
{
    require_once plugin_dir_path(__FILE__) . 'activation.php';
}
