<?php
/*
 * Plugin Name: WP transact.io
 * Description: Integrates transact.io services into WP
 * Version: 0.0.1
 * Author: transact.io
 * Author URI: http://www.transact.io
 */

/**
 * Defining namespace for the plugin
 */
namespace Transact;

/**
 * Requires
 */
use Transact\Admin\DashboardExtension;
require_once( plugin_dir_path(__FILE__) . 'admin/transact-admin.php' );

/**
 * Define all constants
 */
define('TRANSACT_PATH', dirname( __FILE__ ));

/**
 * Hooking functionality to Dashboard
 */
(new DashboardExtension())->hookToDashboard();

