<?php
namespace Transact\Admin;
use Transact\Admin\Settings\Menu\AdminSettingsMenuExtension;

require_once  plugin_dir_path(__FILE__) . '/controllers/transact-admin-settings-menu.php';

/**
 * Class DashboardExtension
 */
class DashboardExtension
{
    /**
     * All hooks to dashboard
     */
    public function hookToDashboard()
    {
        /**
         * Dashboard Menu Hook
         */
        (new AdminSettingsMenuExtension())->hookToDashboard();

        /**
         * Post Settings Hook
         */
    }




}

