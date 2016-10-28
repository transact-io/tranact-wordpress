<?php
namespace Transact\FrontEnd\Controllers\Api;

/**
 * Including transact io php library
 */
require_once  plugin_dir_path(__FILE__) . '/../../vendors/transact-io-php/transact-io.php';

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
     *
     */
    function __construct()
    {
        $this->transact = new \TransactIoMsg();
        $this->transact->setSecret('Signing Secret');
        $this->transact->setAlg('HS256');
    }

    function get_request()
    {
        header('Content-Type: text/javascript; charset=utf8');

        switch($_REQUEST['action']) {

            case 'getToken':
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




    function InitSaleParameters($transact) {

        // Required: set ID of who gets paid
        $transact->setRecipient('5206507264147456');

        // Required:  Set the price of the sale
        $transact->setPrice(2);

        // Required:  Set PROD to use real money,  TEST for testing
        $transact->setClass('PROD');

        // Required:  set URL associated with this puchase
        // User should be able to return to this URL
        $transact->setURL('https://example.site/article1/');

        // Recommended: Title for customer to read for the purchase
        $transact->setTitle('PHP Demo Title');

        $transact->setMethod('CLOSE'); // Optional: by default close the popup


        // Unique code for seller to set to what they want
        //  This could be a code for the item your selling
        $transact->setItem('ItemCode1');

        // Optional Unique ID of this sale
        $transact->setUid('ItemCode1');

        // Set your own meta data
        // Note you must keep this short to avoid going over the 1024 byte limt
        // of the token URL
        $transact->setMeta(array(
            'your' => 'data',
            'anything' => 'you want'
        ));

        return $transact;
    }









    public function is_premium() {
        return false;
    }



}
