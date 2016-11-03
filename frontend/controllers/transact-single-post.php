<?php
namespace Transact\FrontEnd\Controllers\Post;

use Transact\FrontEnd\Controllers\Api\TransactApi;
require_once  plugin_dir_path(__FILE__) . '/transact-api.php';

use Transact\Utils\Config\Parser\ConfigParser;
require_once  plugin_dir_path(__FILE__) . '/../../utils/transact-utils-config-parser.php';


/**
 * Class FrontEndPostExtension
 */
class FrontEndPostExtension
{
    /**
     * text to be included on the button
     */
    const BUTTON_TEXT = 'Purchase on Xsact';

    /**
     * config controller
     * @var
     */
    protected $config;

    /**
     * Post id where this hook is called.
     *
     * @var int
     */
    protected $post_id;

    /**
     * All hooks to single_post template
     */
    public function hookSinglePost()
    {
        $this->config = new ConfigParser();
        add_filter( 'the_content', array($this, 'filter_pre_get_content' ));
        add_action( 'wp_enqueue_scripts', array($this, 'load_js_xsact_library'));

        /**
         * Registering Ajax Calls on single post
         */
        add_action( 'wp_ajax_nopriv_get_token', array($this, 'request_token_callback' ));
        add_action( 'wp_ajax_get_token',        array($this, 'request_token_callback' ));

        /**
         * Registering callback when user buys the item
         */
        add_action( 'wp_ajax_nopriv_get_purchased_content', array($this, 'purchased_content_callback' ));
        add_action( 'wp_ajax_get_purchased_content',        array($this, 'purchased_content_callback' ));
    }

    /**
     * Hooks into content, if the user is premium for that content
     * it will show the premium content for it, otherwise the normal one adding the button to buy on xsact.
     *
     * @param string $content
     * @return string
     */
    public function filter_pre_get_content($content)
    {
        /**
         * If it is not the scope, we return the normal content (could be used in a archive for instance)
         */
        if (!$this->check_scope()) {
            return $content;
        }

        if ((new TransactApi($this->post_id))->is_premium()) {
            $premium_content = get_post_meta( get_the_ID(), 'transact_premium_content' , true ) ;
            return $premium_content;
        } else {
            $button = '<button id="button_purchase" onclick="transactApi.authorize(PurchasePopUpClosed);">' . __(self::BUTTON_TEXT, 'transact') .'</button>';
            return $content . $button;
        }
    }

    /**
     * Loading Transact JS Library
     */
    public function load_js_xsact_library()
    {
        if (!$this->check_scope()) {
            return;
        }

        /**
         * Loading external library (JS API)
         */
        wp_enqueue_script('xsact', $this->config->getJSLibrary());
        $url = array(
            'url' => plugins_url('/ajax/ajax-call.php', __FILE__),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'post_id' => $this->post_id
        );

        /**
         * Loading transact scripts (callbacks)
         */
        wp_register_script( 'transact_callback',  FRONTEND_ASSETS_URL . 'transact_post.js', array('jquery') );
        wp_localize_script( 'transact_callback', 'url', $url );
        wp_enqueue_script(  'transact_callback' );
    }

    /**
     * admin-ajax.php?action=get_token
     * get_token ajax call handler to get and set Token from Transact (onload)
     *
     */
    public function request_token_callback()
    {
        $transact = new TransactApi($_REQUEST['post_id']);
        $token = $transact->get_token();
        header('Content-Type: text/javascript; charset=utf8');
        echo $token;
        exit;
    }

    public function purchased_content_callback()
    {
        $transact = new TransactApi($_REQUEST['post_id']);

        header('Content-Type: text/javascript; charset=utf8');
        try {
            $decoded = $transact->decode_token($_REQUEST['t']);

            //$original_item_code = get_post_meta( $this->post_id, 'transact_item_code', true );
            $premium_content    = get_post_meta( $_REQUEST['post_id'], 'transact_premium_content' , true ) ;

                echo json_encode(array(
                    'content' => $premium_content,
                    'status' => 'OK',
                    'decoded' => $decoded
                ));

        } catch (Exception $e) {

            echo json_encode(array(
                'content' => 'Failed validation',
                'status' => 'ERROR',
                'message' =>  $e->getMessage(),
            ));
        }
        exit;
    }

    /**
     * First we check if the settings have been set on the Dashboard,
     * after:
     * We want the previous filters to work only on the proper scope
     * and that is single posts.
     * todo: check if the client has set up something on the post settings to have premium post
     *
     * @return bool
     */
    public function check_scope()
    {
        /**
         * Setting the post id, is the only scope where I can get it.
         */
        $this->post_id = get_the_ID();

        if (get_transient(SETTING_VALIDATION_TRANSIENT) && is_single() && get_post_type() == 'post') {
            return true;
        } else {
            return false;
        }
    }
}
