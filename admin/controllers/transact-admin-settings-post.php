<?php
namespace Transact\Admin\Settings\Post;

/**
 * Class AdminSettingsPostExtension
 */
class AdminSettingsPostExtension
{
    /**
     * @var Saves the post_id we are handling
     */
    protected $post_id;

    /**
     * All hooks to dashboard
     */
    public function hookToDashboard()
    {
        add_action( 'add_meta_boxes',     array($this, 'add_transact_metadata_post') );
        add_action( 'save_post',          array($this, 'save_meta_box') );
    }

    /**
     * If this class is initiated outside dashboard, we set post_id
     * to ble able to consult metadata
     *
     * @param int|null $post_id
     */
    public function __construct($post_id = null)
    {
        if ($post_id) {
            $this->post_id = $post_id;
        }
    }

    /**
     * Includind transact.io metabox on post
     */
    public function add_transact_metadata_post()
    {
        add_meta_box('transact_metadata', 'transact.io', array($this,'transact_metadata_post_callback'), 'post', 'advanced');
    }

    /**
     * Hook when saving post, to save/update values
     * @param $post_id
     */
    public function save_meta_box( $post_id )
    {
        /**
         * First, we check if setting have been set in the plugin
         */
        $transact_setting_transient = get_transient(SETTING_VALIDATION_TRANSIENT);
        if (!$transact_setting_transient) {
            return;
        }

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['transact_inner_custom_box_nonce'] ) ) {
            return $post_id;
        }

        $nonce = $_POST['transact_inner_custom_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'transact_inner_custom_box' ) ) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        /**
         * Create transact item code first time post is used
         */
        $transact_item_code = get_post_meta( $post_id, 'transact_item_code' );
        if (empty($transact_item_code)) {
            $transact_item_code = md5($post_id . time());
            update_post_meta( $post_id, 'transact_item_code', $transact_item_code );

        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $mydata[1] = sanitize_text_field( $_POST['transact_price'] );
        // Update the meta field.
        update_post_meta( $post_id, 'transact_price', $mydata[1] );

        $content = htmlspecialchars( $_POST['transact_premium_content'] );
        update_post_meta( $post_id, 'transact_premium_content', $content );
    }


    /**
     * Creating callback to include transact settings on post
     *
     * @param $post
     */
    public function transact_metadata_post_callback($post)
    {
        /**
         * First, we check if setting have been set in the plugin
         */
        $transact_setting_transient = get_transient(SETTING_VALIDATION_TRANSIENT);
        if (!$transact_setting_transient) {
            _e('Please, You need to activate your transact settings properly', 'transact');
            return;
        }

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'transact_inner_custom_box', 'transact_inner_custom_box_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $value[1] = get_post_meta( $post->ID, 'transact_price', true );
        $value[2] = get_post_meta( $post->ID, 'transact_item_code', true );
        $value[3] = get_post_meta( $post->ID, 'transact_premium_content' , true ) ;


        /**
         * Premium Content
         */
        _e('<h3>Premium Content</h3>', 'transact');
        wp_editor( htmlspecialchars_decode($value[3]), 'transact_premium_content');

        /**
         * Rest of the form price
         */
        ?>
        <br/>
        <label for="transact_price">
            <?php _e( 'Premium Price', 'transact' ); ?>
        </label>
        <input type="text" id="transact_price" name="transact_price" value="<?php echo esc_attr( $value[1] ); ?>" />
        <br/>
        <label for="transact_item_code">
            <?php _e( 'Item Code', 'transact' ); ?>
        </label>
        <input readonly type="text" size="35" id="transact_item_code" name="transact_item_code" value="<?php echo esc_attr( $value[2] ); ?>" />
        <br/>
        <?php

    }

    /**
     * Get Transact price
     * @return int
     */
    public function get_transact_price()
    {
        return get_post_meta( $this->post_id, 'transact_price', true );
    }

    /**
     * Get Transact item code
     * @return int
     */
    public function get_transact_item_code()
    {
        return get_post_meta( $this->post_id, 'transact_item_code', true );
    }

}

