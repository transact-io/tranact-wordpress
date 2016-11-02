<?php
namespace Transact\FrontEnd\Controllers\Api;

/**
 * Including transact io php library
 */
require_once  plugin_dir_path(__FILE__) . '/../../vendors/transact-io-php/transact-io.php';

/**
 * We need admin settings menu to rescue the settings
 */
require_once  plugin_dir_path(__FILE__) . '/../../admin/controllers/transact-admin-settings-menu.php';
use Transact\Admin\Settings\Menu\AdminSettingsMenuExtension;

/**
 * We need admin settings menu to rescue the settings
 */
require_once  plugin_dir_path(__FILE__) . '/../../admin/controllers/transact-admin-settings-post.php';
use Transact\Admin\Settings\Post\AdminSettingsPostExtension;


/**
 * Class TransactApi
 */
class TransactApi
{
    /**
     * @var \TransactIoMsg
     */
    public $transact;

    /**
     * @var int
     */
    public $post_id;

    /**
     * All information to set on Transact Io Object
     */
    protected $recipient_id;
    protected $secret_id;
    protected $env;

    protected $price;
    protected $item_id;
    protected $item_name;

    protected $article_title;
    protected $article_url;

    protected $method;
    protected $alg;

    /**
     * @param $post_id
     * @throws \Exception
     */
    function __construct($post_id)
    {
        $this->post_id = $post_id;
        $this->get_transact_information();
        $this->transact = new \TransactIoMsg();
    }

    /**
     * Set All info needed for Transact
     * We grab the information from Transact Settings (Dashboard)
     * and specific post.
     *
     */
    function get_transact_information()
    {
        $settings_menu_dashboard = new AdminSettingsMenuExtension();
        $this->recipient_id = $settings_menu_dashboard->get_account_id();
        $this->secret_id    = $settings_menu_dashboard->get_secret();
        $this->env          = strtoupper($settings_menu_dashboard->get_env()); // TEST or PROD

        $settings_post = new AdminSettingsPostExtension($this->post_id);
        $this->price     = $settings_post->get_transact_price();
        $this->item_id   = $settings_post->get_transact_item_code();
        $this->item_name = $settings_post->get_transact_item_code();

        $this->article_title = get_the_title($this->post_id);
        $this->article_url   = get_permalink($this->post_id);

        /**
         * todo: what are the options?
         */
        $this->method = 'CLOSE';
        $this->alg    = 'HS256';

    }

    /**
     * Gets token to set up on transact js library
     *
     * @return string return token json {"token":"xxx"}
     */
    function get_token()
    {
        $transact = $this->init_sale_parameters($this->transact);

        $response = array(
            'token' => $transact->getToken()
        );
        return json_encode($response);
    }

    /**
     * Init library with sales parameter for a given article
     * 
     * @param \TransactIoMsg $transact
     * @return \TransactIoMsg
     */
    function init_sale_parameters($transact)
    {
        $transact->setSecret($this->secret_id);
        $transact->setAlg($this->alg);

        // Required: set ID of who gets paid
        $transact->setRecipient($this->recipient_id);

        // Required:  Set the price of the sale
        //todo: what is max value?
        $transact->setPrice($this->price);

        // Required:  Set PROD to use real money,  TEST for testing
        $transact->setClass($this->env);

        // Required:  set URL associated with this puchase
        // User should be able to return to this URL
        $transact->setURL($this->article_url);

        // Recommended: Title for customer to read for the purchase
        $transact->setTitle($this->article_title);

        $transact->setMethod($this->method); // Optional: by default close the popup


        // Unique code for seller to set to what they want
        //  This could be a code for the item your selling
        //todo: what is this?
        $transact->setItem($this->item_name);

        // Optional Unique ID of this sale
        $transact->setUid($this->item_id);

        // Set your own meta data
        // Note you must keep this short to avoid going over the 1024 byte limt
        // of the token URL
        //todo: what ist his?
        $transact->setMeta(array(
            'your' => 'data',
            'anything' => 'you want'
        ));

        return $transact;
    }


    function get_request()
    {
        header('Content-Type: text/javascript; charset=utf8');

        switch($_REQUEST['action']) {

            case 'get_token':
                $transact = InitSaleParameters($this->transact);

                $response = array(
                    'token' => $transact->getToken()
                );
                echo json_encode($response);
                break;

            case 'getPurchasedContent':

                try {
                    $decoded = $this->transact->decodeToken($_REQUEST['t']);
                    echo json_encode(array(
                        'content' => 'SUCESSS PAID CONTENT HERE!',
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

                break;
            default:
                header("HTTP/1.0 404 Not Found");

                echo json_encode(new ErrorResponse('404', 'Invalid API call'));
                exit;
        };
    }








    public function is_premium() {
        return false;
    }



}
