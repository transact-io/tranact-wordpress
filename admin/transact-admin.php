<?php
namespace Transact\Admin;

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
        add_action( 'admin_menu', array($this, 'add_transact_menu' ));

    }

    public function add_transact_menu()
    {
        add_menu_page( 'transact.io', 'transact.io', 'manage_options', 'transact-admin-page.php', array($this, 'transact_io_admin_callback'), 'dashicons-cart' );
    }

    public function transact_io_admin_callback()
    {
        ?>
        <div class="wrap">
            <h2>transact.io Account</h2>
        </div>
        <?php
    }


}

