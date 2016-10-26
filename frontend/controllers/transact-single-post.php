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
     * All hooks to single_post template
     */
    public function hookSinglePost()
    {
        $this->config = new ConfigParser();
        add_filter( 'the_content', array($this, 'filter_pre_get_content' ));
        add_action('wp_enqueue_scripts', array($this, 'load_js_xsact_library'));
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

        if ((new TransactApi())->is_premium()) {
            $premium_content = get_post_meta( get_the_ID(), 'transact_premium_content' , true ) ;
            return $premium_content;
        } else {
            $button = '<button onclick="transactApi.authorize(PurchasePopUpClosed);">' . __(self::BUTTON_TEXT, 'transact') .'</button>';
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

        wp_enqueue_script('xsact', $this->config->getJSLibrary());
    }

    /**
     * We want the previous filters to work only on the proper scope
     * and that is single posts.
     *
     * @return bool
     */
    public function check_scope()
    {
        if (is_single() && get_post_type() == 'post') {
            return true;
        } else {
            return false;
        }
    }
}
