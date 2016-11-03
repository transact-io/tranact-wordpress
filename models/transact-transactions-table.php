<?php
namespace Transact\Models\transactTransactionsTable;


/**
 * Class transactTransactionsModel
 */
class transactTransactionsModel
{
    /**
     * Table constant name
     */
    const TRANSACTIONS_TABLE_NAME = 'transact_transactions';

    /**
     * WP DB connector
     *
     * @var \wpdb
     */
    protected $connector;

    /**
     * Table Name
     *
     * @var string
     */
    protected $table_name;

    /**
     * Array that holds the result
     *
     * @var array
     */
    protected $results;


    function __construct()
    {
        global $wpdb;
        $this->connector = $wpdb;
        $this->table_name = $wpdb->prefix . self::TRANSACTIONS_TABLE_NAME;
    }

    /**
     * Retrieves all rows from a given post id
     *
     * @param $post_id
     * @return array
     */
    public function get_transactions_by_post_id($post_id)
    {
        $sql = $this->connector->prepare("SELECT * FROM $this->table_name WHERE post_id='%s'", $post_id);
        $this->results = $this->connector->get_results( $sql, ARRAY_A );
        return $this->beautify_result($this->results);
    }

    /**
     * @param $sale_id
     * @return array
     */
    public function get_transaction_by_sale_id($sale_id)
    {
        $sql = $this->connector->prepare("SELECT * FROM $this->table_name WHERE sale_id='%s'", $sale_id);
        $this->results = $this->connector->get_results( $sql, ARRAY_A );
        return $this->beautify_result($this->results);
    }

    /**
     * Create a transaction record on the DB
     *
     * @param $post_id
     * @param $sale_id
     * @param $timestamp
     * @return bool
     */
    public function create_transaction($post_id, $sale_id, $timestamp)
    {
        try {
            $row = $this->connector->insert(
                $this->table_name,
                array(
                    'post_id' => $post_id,
                    'sale_id' => $sale_id,
                    'timestamp' => $timestamp
                ),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );
            if ($row) {
                return true;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Returns empty, one result or array of results
     * @param $results
     * @return array
     */
    private function beautify_result($results)
    {
        if (empty($results)) {
            return array();
        } else if (count($results) == 1) {
            return current($results);
        } else {
            return $results;
        }
    }
}
