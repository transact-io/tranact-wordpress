<?php
namespace Transact\FrontEnd\Controllers\Cookie;

use Transact\Models\transactTransactionsTable\transactTransactionsModel;
require_once  plugin_dir_path(__FILE__) . '../../models/transact-transactions-table.php';


/**
 * Class CookieManager
 */
class CookieManager
{
    const COOKIE_NAME = 'wp_transact_';

    /**
     * @var array|null
     */
    protected $cookie;



    function __construct()
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $this->cookie = json_decode(stripslashes($_COOKIE[self::COOKIE_NAME]));
        }
    }



    function validate_cookie($post_id)
    {
        if($this->cookie) {
            foreach ($this->cookie as $cookie) {
                if ($cookie->id == $post_id) {
                    $transactModel = new transactTransactionsModel();
                    $transaction = $transactModel->get_transaction_by_sale_id($cookie->uid);
                    if (!empty($transaction) && $transaction['post_id'] == $post_id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }




}