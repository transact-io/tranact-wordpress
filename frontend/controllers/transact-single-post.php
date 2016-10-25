<?php
namespace Transact\FrontEnd\Controllers\Post;

use Transact\FrontEnd\Controllers\Api\TransactApi;
require_once  plugin_dir_path(__FILE__) . '/transact-api.php';


/**
 * Class FrontEndPostExtension
 */
class FrontEndPostExtension
{
    /**
     * All hooks to single_post template
     */
    public function hookSinglePost()
    {
        add_filter( 'the_content', array($this, 'filter_pre_get_content' ));
    }

    /**
     * Hooks into content, if the user is premium for that content
     * it will show the premium content for it, otherwise the normal one.
     *
     * @param string $content
     * @return string
     */
    public function filter_pre_get_content($content)
    {
        if ((new TransactApi())->is_premium())
        {
            $premium_content = get_post_meta( get_the_ID(), 'transact_premium_content' , true ) ;
            return $premium_content;
        }

        return $content;
    }
}
