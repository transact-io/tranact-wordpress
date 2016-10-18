<div class="wrap">
    <h1>transact.io Account</h1>
</div>
<div class="clear"></div>
<form method="post" action="options.php">
    <?php
        settings_fields( 'transact-settings' );
        do_settings_sections( 'transact-settings' );
        submit_button();
    ?>
</form>